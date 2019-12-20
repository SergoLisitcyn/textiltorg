<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if (!empty($arResult)): ?>
<div class="header-catalog-menu mn_content">
        <div id="header-drop-down-menu">
<img src="http://dev.textiletorg.ru/bitrix/templates/textiletorg/components/bitrix/menu/catalog-popular/img/catalog-small.png" alt="���� �������">
   <div class="container-header-menu">
                <div class="close">?</div>
                <div class="mn-ul-items">
                    <? foreach ($arResult as $id => $item): ?>
                        <? $cssClass = ($id == 1) ? "middle" : "left" ?>
                        <div class="mn-ul-item <?= $cssClass ?>">
                            <p><?= $item['title'] ?></p>
                            <ul class="mn-ul">
                                <? foreach ($item['items'] as $nChild => $itemChild): ?>
                                    <li class="mn_parent_cat">
                                        <a href="<?= $itemChild["LINK"] ?>">
                                            <i class="tt-icons <?= $itemChild["ICON"] ?>"></i>
                                            <span><?= $itemChild["TEXT"] ?></span>
                                        </a>
                                    </li>
                                <? endforeach ?>
                            </ul>
                        </div>
                    <? endforeach ?>
                </div>
            </div>
        </div>
        <div class="header-full-menu-hint"></div>
        <div class="catalog-menu-substrate"></div>
    </div>
<? endif ?>