<?if ($arResult['ID']):?>
    <script>
    var _tmr = window._tmr || (window._tmr = []);
    _tmr.push({id: "<?=$arResult['ID']?>", type: "pageView", start: (new Date()).getTime()});
    (function (d, w, id) {
      if (d.getElementById(id)) return;
      var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
      ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";
      var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
      if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
    })(document, window, "topmailru-code");
    </script><noscript><div style="position:absolute;left:-10000px;">
    <img src="//top-fwz1.mail.ru/counter?id=<?=$arResult['ID']?>;js=na" style="border:0;" height="1" width="1" alt="Рейтинг@Mail.ru" />
    </div></noscript>
<?endif?>

<?if ($arResult['JSON']):?>
    <script>
        var _tmr = _tmr || [];
        _tmr.push(<?=$arResult['JSON']?>);
    </script>
<?endif?>