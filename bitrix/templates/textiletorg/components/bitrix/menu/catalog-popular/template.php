<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if (!empty($arResult)): ?>

<div class="catalog-popular n_desc_only">
<div class="image-menu"><h2 class="hidden-xs hidden-sm header-index">Популярные категории товаров</h2><img id="open_menu" src="/bitrix/templates/textiletorg/components/bitrix/menu/catalog-popular/img/catalog-small.png" alt="Весь каталог"></div>
<ul>
      <? foreach ($arResult as $id => $item): ?>
            <li>
                <div class="wi_img"><i class="tt-icons <?= $item['parent']['ICON'] ?>"></i></div>
                <div class="wi_text">
                    <div class="wi_text-head"><?= $item['parent']['TEXT'] ?></div>
                    <ul class="list">
                        <? foreach ($item['child'] as $nChild => $itemChild): ?>
                            <li class="mn_parent_cat">
                                <a href="<?= $itemChild["LINK"] ?>">
                                    <span><?= $itemChild["TEXT"] ?></span>
                                </a>
                            </li>
                        <? endforeach ?>
                    </ul>
                </div>
            </li>
            <? endforeach ?>
        </ul>
 </div>

    <section class="slider n_mobile_only" id = "n_catalog_popular">
        <div class="flexslider catalog-popular">
            <ul class="slides">
                <? foreach ($arResult as $id => $item): ?>
                    <li>
                        <div style = "width:100%;" >
                            <div style = "margin: 0 auto; width: 45%;">
                                <div class="wi_img"><i class="tt-icons <?= $item['parent']['ICON'] ?>"></i></div>
                                <div class="wi_text">
                                    <div class="wi_text-head"><?= $item['parent']['TEXT'] ?></div>
                                    <ul class="list">
                                        <? foreach ($item['child'] as $nChild => $itemChild): ?>
                                            <li class="mn_parent_cat">
                                                <a href="<?= $itemChild["LINK"] ?>">
                                                    <span><?= $itemChild["TEXT"] ?></span>
                                                </a>
                                            </li>
                                        <? endforeach ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                <? endforeach ?>
            </ul>
        </div>
    </section>

<? endif ?>