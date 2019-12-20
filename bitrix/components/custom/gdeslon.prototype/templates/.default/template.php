<?if ($arResult['PAGE_TYPE'] == 'main'):?>
    <script src="https://www.gdeslon.ru/landing.js?mode=main&mid=<?=$arResult['MID']?>"></script>
<?elseif ($arResult['PAGE_TYPE'] == 'card'):?>
    <script src="https://www.gdeslon.ru/landing.js?mode=card&codes=<?=$arResult['CODES']?>&mid=<?=$arResult['MID']?>"></script>
<?elseif ($arResult['PAGE_TYPE'] == 'list'):?>
    <script src="https://www.gdeslon.ru/landing.js?mode=list&codes=<?=$arResult['CODES']?>&mid=<?=$arResult['MID']?>&cat_id=<?=$arResult['CAT_ID']?>"></script>
<?elseif ($arResult['PAGE_TYPE'] == 'search'):?>
    <script src="https://www.gdeslon.ru/landing.js?mode=list&codes=<?=$arResult['CODES']?>&mid=<?=$arResult['MID']?>"></script>
<?elseif ($arResult['PAGE_TYPE'] == 'basket'):?>
    <script src="https://www.gdeslon.ru/landing.js?mode=basket&codes=<?=$arResult['CODES']?>&mid=<?=$arResult['MID']?>"></script>
<?elseif ($arResult['PAGE_TYPE'] == 'thanks-tmp'):?>
    <script src="https://www.gdeslon.ru/landing.js?mode=thanks&codes=<?=$arResult['CODES']?>&order_id=<?=$arResult['ORDER']?>&mid=<?=$arResult['MID']?>"></script>
<?else:?>
    <script src="https://www.gdeslon.ru/landing.js?mode=other&mid=<?=$arResult['MID']?>"></script>
<?endif?>
