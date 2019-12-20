var center_menu_data = {
    1: {
        0: {url: '/shveynye-mashiny/', img: '/mod_files/img/main_center_menu/shveynye-mashiny.jpg', caption: 'Швейные машины'},
        1: {url: '/overloki/', img: '/mod_files/img/main_center_menu/overloki.png', caption: 'Оверлоки'},
        2: {url: '/manekeny/', img: '/mod_files/img/main_center_menu/manekeny.png', caption: 'Манекены'},
		3: {url: '/aksessuary-dlya-shitya/', img: '/mod_files/img/main_center_menu/acses_shitya.png', caption: 'Аксессуары для шитья'},

    },
    2: {
        0: {url: '/vyshivalnye-mashiny/', img: '/mod_files/img/main_center_menu/vyshivalnye.png', caption: 'Вышивальные машины'},
        1: {url: '/shvejno-vyshivalnye-mashiny/', img: '/mod_files/img/main_center_menu/shveyno-vyshivalnye.jpg', caption: 'Швейно-вышивальные'},
		2: {url: '/aksessuary-dlya-vyshivaniya/', img: '/mod_files/img/main_center_menu/acses_vishiv.png', caption: 'Аксессуары для вышивания'},
    },
    3: {
        0: {url: '/vyazalnye-mashiny/', img: '/mod_files/img/main_center_menu/vyazalnye.jpg', caption: 'Вязальные машины'},
        1: {url: '/tkackie-stanki/', img: '/mod_files/img/main_center_menu/tkatskiy-stanok.jpg', caption: 'Ткацкие станки'},
		2: {url: '/aksessuary-dlya-vyazaniya/', img: '/mod_files/img/main_center_menu/acses_vyaz.png', caption: 'Аксессуары для вязания'},
		3: {url: '/kettelnye-mashiny/', img: '/mod_files/img/main_center_menu/main_page_karetki.png', caption: 'Кеттельные машины'},
		4: {url: '/aksessuary-dlya-vyazaniya/prinadlezhnosti/', img: '/mod_files/img/main_center_menu/main_page_ketelmash.png', caption: 'Каретки и доп. принадлежности'},
    },
    4: {
        0: {url: '/gladilnaja-tehnika/gladilnye-pressy/', img: '/mod_files/img/main_center_menu/gladilnye-pressy.png', caption: 'Гладильные прессы'},
        1: {url: '/gladilnaya-tehnika/gladilnye-doski/', img: '/mod_files/img/main_center_menu/gladilnye-doski.png', caption: 'Гладильные доски'},
        2: {url: '/gladilnaja-tehnika/gladilnye-sistemy/', img: '/mod_files/img/main_center_menu/gladilnye-sistemy.png', caption: 'Гладильные системы'},
        3: {url: '/gladilnaja-tehnika/otparivateli/', img: '/mod_files/img/main_center_menu/otparivateli.png', caption: 'Отпариватели'},
        4: {url: '/gladilnaja-tehnika/parogeneratory/', img: '/mod_files/img/main_center_menu/parogeneratory.png', caption: 'Парогенераторы'},
		5: {url: '/gladilnaja-tehnika/aksessuary-dlya-glazheniya/', img: '/mod_files/img/main_center_menu/aksesuari-dly-gla.png', caption: 'Аксессуары для глажения'},
        6: {url: '/gladilnaja-tehnika/sushilki-dlja-belja/', img: '/mod_files/img/main_center_menu/sushilki-dlya-belya.png', caption: 'Сушилки для белья'},
        7: {url: '/gladilnaja-tehnika/utjugi/', img: '/mod_files/img/main_center_menu/main_page_utugi.png', caption: 'Утюги'},
    },
    5: {
        0: {url: '/vse-dlja-uborki/paroochistitel/', img: '/mod_files/img/main_center_menu/paroochistiteli.png', caption: 'Пароочистители'},
        1: {url: '/vse-dlja-uborki/pylesosy/', img: '/mod_files/img/main_center_menu/pylesosy.png', caption: 'Пылесосы'},
		2: {url: '/vse-dlya-uborki/parovye_shvabry/', img: '/mod_files/img/main_center_menu/parovye_shvabry.png', caption: 'Паровые швабры'},
    },
    6: {
        //0: {url: '', img: '/mod_files/img/main_center_menu/uslugi.jpg', caption: 'Услуги'},
        1: {url: '/prochee/podarochnye-karty/', img: '/mod_files/img/main_center_menu/karty.png', caption: 'Подарочные карты'},
    }

};

$(document).ready(function(){
    $('.overlay_main_center').on('click', function(){
        $(this).hide();
        $('#wm_main_center_menu').hide();
    });
});
function wm_close(){
    $('.overlay_main_center').hide();
    $('#wm_main_center_menu').hide();

}
function show_center_menu(id, sender){

    var title = $(sender).closest(".main_center_menu_plit_big").find(".mcm_text").text();
    $("#wm_main_center_menu_title").text(title);
var out = '',
    content = $('#content_main_center_menu'),
    count = 0;

out += '<ul>';
console.log(window.center_menu_data);
	$.each(window.center_menu_data[id], function(){
        out += '<li>'+
             '<a href="'+this.url+'">'+
             '<div class="img"><img src="'+this.img+'"></div>'+
             '<div class="text"><div class="cell">'+this.caption+'</div></div>'+
             '</a>'+
         '</li>';
         count++;
	});

out += '</ul>';
content.html(out);

var top=0, w=652;

    if (id <= 3) {
        w = count*190;
        top = -280;
    } else if (id == 4 || id == 5) {
        w = count*190;
        top = -82;
    } else {
        w = count*190;
        top = 116;
    }


    var offset = $('html').offset();

$('.overlay_main_center').show();
$('#wm_main_center_menu').show();
//if(id>3) $('#wm_main_center_menu').css({'margin-top': '200px'}); else $('#wm_main_center_menu').css({'margin-top': '-200px'});
//$('#wm_main_center_menu').css({'max-width': w+'px'}).animate({'margin-top': top+'px'}, 300);
$('#wm_main_center_menu').css({top: (-offset.top) - ($('#wm_main_center_menu').height()/2) - 100 });

}