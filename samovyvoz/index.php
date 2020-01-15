<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Специально для Вас мы открываем не просто пункты выдачи товаров, а полноценные специализированные магазины! В сети магазинов ТекстильТорг Вы сможете бесплатно попробовать любую технику перед покупкой");
$APPLICATION->SetPageProperty("keywords", "самовывоз товаров из текстильторг, швейная техника самовывоз, бесплатная проба швейной машинки");
$APPLICATION->SetPageProperty("title", "Самовывоз товаров из нашего магазина. Проба швейной машинки | ТекстильТорг");
$APPLICATION->SetTitle("Самовывоз — удобная возможность для тех, кто хочет сэкономить время и деньги");
?><div class="m-hidden">
 <img src="/bitrix/templates/textiletorg/aks-img/o-nas/samomyvoz.png" style="float: right; margin: 0 0px 5px 40px;" alt="">
	<?$APPLICATION->IncludeComponent(
	"custom:region-select.prototype",
	"footer",
	Array(
		"DEFAULT_REGION_CITY_NAME" => "Москва",
		"DEFAULT_REGION_ID" => "19"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
</div>
 <?$APPLICATION->IncludeComponent(
	"ayers:stores.page",
	"",
	Array(
		"CACHE_TIME" => 36000,
		"CITY" => $GLOBALS["GEO_REGION_CITY_NAME"],
		"COUNT_SHOW" => 3
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
<p>
	В торговых точках «ТекстильТорга» у Вас всегда есть возможность ознакомиться с нашей продукцией максимально полно и всесторонне. Вы можете абсолютно бесплатно попробовать в деле любой понравившийся товар.
</p>
 <?$APPLICATION->IncludeComponent(
	"ayers:stores.map",
	"",
	Array(
		"CACHE_TIME" => 36000,
		"CITY" => $GLOBALS["GEO_REGION_CITY_NAME"]
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
<div class="clear">
</div>
<div class="adantages-samovyvoz">
    <b>Преимущества самовывоза</b>
</div>
<br>
<div class="container-samovyvoz">
    <ul>
        <li>
            <div class="catalog-aks-image">
                <img src="/bitrix/templates/textiletorg/aks-img/o-nas/samovyvoz-1.png">

                <div class="catalog-aks-text">
                    <span>Экономия времени</span>
                </div>
            </div>
        </li>
        <li>
            <div class="catalog-aks-image">
                <img src="/bitrix/templates/textiletorg/aks-img/o-nas/samovyvoz-2.png">

                <div class="catalog-aks-text">
                <span>Экономия денег на доставку</span>
            </div>
            </div>
        </li>
        <li>
            <div class="catalog-aks-image">
                <div class="catalog-aks-text">
                    <img src="/bitrix/templates/textiletorg/aks-img/o-nas/samovyvoz-3.png">
                    <span>Возможность попробовать перед покупкой</span>
                </div>
            </div>
        </li>
        <li>
            <div class="catalog-aks-image">
                <div class="catalog-aks-text">
                    <img src="/bitrix/templates/textiletorg/aks-img/o-nas/samovyvoz-4.png">
                    <span>Возможность изменить выбор</span>
                </div>
            </div>
        </li>
    </ul>
<!--	<div class="container-samovyvoz-ul">-->
<!--		<div class="adantage-1">-->
<!--		</div>-->
<!--		<div>-->
<!--			Экономия времени-->
<!--		</div>-->
<!--	</div>-->
<!--	<div class="container-samovyvoz-ul">-->
<!--		<div class="adantage-2">-->
<!--		</div>-->
<!--		<div>-->
<!--			Экономия денег на доставку-->
<!--		</div>-->
<!--	</div>-->
<!--	<div class="container-samovyvoz-ul">-->
<!--		<div class="adantage-3">-->
<!--		</div>-->
<!--		<div>-->
<!--			Возможность попробовать перед покупкой-->
<!--		</div>-->
<!--	</div>-->
<!--	<div class="container-samovyvoz-ul">-->
<!--		<div class="adantage-4">-->
<!--		</div>-->
<!--		<div>-->
<!--			Возможность изменить выбор-->
<!--		</div>-->
<!--	</div>-->
</div>
<div style="clear: both;">
</div>
 <br>
<p>
	В магазинах «ТекстильТорг» у Вас всегда есть возможность ознакомиться с нашей продукцией максимально полно и всесторонне. Вы можете абсолютно бесплатно попробовать в деле любой понравившийся товар. Наши эксперты-консультанты научат Вас заправлять швейную машинку, помогут подобрать нитки и иглы для шитья, расскажут как выбрать и настроить нужную программу и многое другое. Вы сами сможете отпарить одежду, ощутить возможности гладильной системы, проверить качество вышивки и безупречность стежка.
</p>
<p>
	Наши продавцы не только ответят на все Ваши вопросы, но и покажут в деле любую приглянувшуюся Вам модель.
</p>
<p>
	Стоимость товара, указанная в каталоге нашего магазина, является окончательной и неизменной на день покупки. В нее уже включены все налоги.
</p>
<p>
	При самовывозе цена на товар не меняется, Ваша экономия заключается в отсутствии оплаты доставки!
</p>
<p>
	Выбирайте и резервируйте товар на сайте. Забрать свой заказ Вы сможете в любой день недели — <a href="/poluchenie/nashi-magaziny/">пункты выдачи</a> работают ежедневно (без выходных) с 09:00 до 21:00.
</p>
 <br>
<br>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "block_feedback",
		"COMPONENT_TEMPLATE" => ".default",
		"EDIT_TEMPLATE" => ""
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>