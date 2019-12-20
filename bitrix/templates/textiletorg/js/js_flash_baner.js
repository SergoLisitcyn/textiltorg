function inner_flash(bnFlash, bnImg, bnAlt, bnFlashUrl, bnW, bnH) {
/* 	bnFlash - путь к файлу с flash
	rassrochka.gif путь к картинке
	bnAlt - подсказка
	bnCount = "/img/sharp.gif"; - картинка затычка
	bnFlashUrl = "/akcii/rassrochka.html"; - url на страницу
	bnImgUrl = "/akcii/rassrochka.html"; - url на страницу
	bnW = 209; - ширина
	bnH = 96; - высота
*/

var flashV = "6";
var IEonly = "0";

    var H = Math.round(Math.random() * 1048576), D = window, F = navigator, L = "";
    var I = (!bnFlashUrl) ? false : true, N = (F.userAgent && (F.userAgent.indexOf("MSIE") >= 0) && (F.appVersion.indexOf("Win") != -1)) ? 1 : 0, G = (F.mimeTypes && F.mimeTypes["application/x-shockwave-flash"]) ? F.mimeTypes["application/x-shockwave-flash"].enabledPlugin : 0, M = (F.userAgent.toLowerCase().indexOf("gecko/") != -1) ? 1 : 0;
    if (G) {
        var S = parseInt(G.description.split("Shockwave Flash ")[1]), O = (S >= flashV)
    } else {
        if (N) {
            elem.innerHTML+='<script type="text/vbscript">\non error resume next\n isIEPlay = IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.' + flashV + '"))\n <\/script>\n';
            O = D.isIEPlay
        }
    }
    if (IEonly && (IEonly == 1) && !N) {
        O = 0
    }
    var R, C, U, E;
    if (D.flW) {
        R = "100%";
        U = "100%"
    } else {
        R = bnW;
        U = bnW + "px"
    }
    if (D.flH) {
        C = D.flH;
        E = D.flH
    } else {
        C = bnH;
        E = bnH + "px"
    }
    var A = "width:" + U + " ! important; height:" + E + " ! important;", Q = ' width="' + R + '" height="' + C + '"', K = ' width="' + bnW + '" height="' + bnH + '"', V = "";
    if (I) {
        V += '<a href="' + bnFlashUrl + '" target="_blank">'
    }
    V += '<img src="' + bnImg + "?rnd=" + H + '"' + K + ' title="' + bnAlt + '" alt="' + bnAlt + '" border="0" />';
    if (I) {
        V += "</a>"
    }
    var T = "";
    if (O && bnFlash.length>0) {
        if (D.flW) {
            L += '<div style="width:' + D.flW + ' !important;">'
        }
        var P = '<param name="quality" value="high" /><param name="wmode" value="opaque" /><param name="bgcolor" value="#ffffff" />', B = bnFlash + "?rnd=" + H;
        if (I) {
            B += "&link1=" + bnFlashUrl
        }        
        if (N) {
            L += '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"' + Q + ">";
            L += '<param name="movie" value="' + B + '" />' + P + "</object>"
        } else {
            if (M) {
                L += '<div id="if-flashblock-' + H + '">'+
						'<div style="position: relative; display: inline-block"><a style="position: absolute; width: 100%; height: 100%; left: 0; top: 0;" href="'+bnFlashUrl+'" target="_blank" alt="' + bnAlt + '" title="' + bnAlt + '"></a>'+
						'<embed style="' + A + '" src="' + B + '"' + Q + ' quality="high" wmode="opaque" bgcolor="#ffffff" type="application/x-shockwave-flash"></embed></div></div>';
            } else {
				L += '<div style="position: relative; display: inline-block"><a style="position: absolute; width: 100%; height: 100%; left: 0; top: 0;" href="'+bnFlashUrl+'" target="_blank" alt="' + bnAlt + '" title="' + bnAlt + '"></a>';
                L += '<object type="application/x-shockwave-flash" data="' + B + '"' + Q + ">" + P + "</object></div>"
            }
        }
        if (D.flW) {
            T = ' style="width: ' + D.bnW + 'px"'
        }
    } else {
        L += V
    }
    
    if (D.flW) {
        L += "</div>"
    }
    elem.innerHTML +=L;
    if (M && O) {
        var J = new Image();
        J.onload = function() {
            window.addEventListener("load", function() {
                var X = 0;
                function W() {
                    var Y = document.getElementById("if-flashblock-" + H);
                    if (Y) {
                        if (!Y.getElementsByTagName("embed").length) {
                            Y.innerHTML = V;
                            Y.style.height = bnH + "px ! important"
                        } else {
                            if (X < 5) {
                                X++;
                                setTimeout(W, 200)
                            }
                        }
                    }
                }
                setTimeout(W, 200)
            }, false)
        };
        J.src = "chrome://flashblock/skin/flash-on-24.png"
    }
}