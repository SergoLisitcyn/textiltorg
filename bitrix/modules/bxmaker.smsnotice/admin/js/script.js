$(document).ready(function () {

    var list_page = $('.bxmaker_smsnotice_list_table_box');
    if (list_page.length) {


        var send_box = $('.ap-smsnotice-send-box');
        var textarea = send_box.find('textarea[name="text"]');
        var btnTranslit = send_box.find('[name="translit"]');
        var textarea_box = send_box.find('.textarea_box');
        var template_type = send_box.find('select[name="template"]');
        var result_msg_text = send_box.find('.result_msg_text');
        var fileds_box = send_box.find('.fileds_box');
        var info_box = send_box.find('.info_box');

        // отправка смс
        $('.ap-smsnotice-send-box').each(function () {
            var box = $(this);
            var self = {},
                msgBox = box.find('.msg_box');

            //сообщение
            self.showMsg = function (msg, error) {
                var error = error || false;
                msgBox.removeClass('error success').empty().fadeOut(300);

                if (msg == undefined) return;

                if (error) msgBox.addClass('error').html(msg);
                else msgBox.addClass("success").html(msg);
            };

            //отправка смс
            box.find('.btn_send').on("click", function () {
                var btn = $(this);
                if (btn.hasClass('preloader')) return;
                btn.addClass('preloader');

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        sessid: BX.bitrix_sessid(),
                        method: 'send_sms',
                        phone: box.find('input[name="phone"]').val(),
                        text: getRealText()
                    },
                    error: function (r) {
                        self.showMsg('Error connection!', true);
                        btn.removeClass('preloader');
                    },
                    success: function (r) {
                        if (r.status == 1) {
                            self.showMsg(r.response.msg);
                        }
                        else if (r.status == 0) {
                            self.showMsg(r.error.error_msg, true);
                        }
                        btn.removeClass('preloader');

                        updateList();
                    }
                })
            });

            // очистка поля с текстом
            box.find('.btn_clean').on("click", function () {
                if (template_type.val() == '0') {
                    textarea.val('');
                    changeText();
                }
            });

            //подсчет введенных символов
            textarea.on("keyup", changeText);

            // вывод полей из шаблона
            template_type.on("change", changeTemplateType);

            //транслит
            btnTranslit.on("change", changeText);

            //заполнение полей
            fileds_box.on("keyup", 'input', changeText);
        });

        function updateList(){
            $.ajax({
                type: 'POST',
                dataType: 'html',
                data: {
                    sessid: BX.bitrix_sessid(),
                    method: 'get_content'
                },
                error: function (r) {

                },
                success: function (r) {
                   list_page.html(r);
                }
            })
        }

        // подготовка полей для ввода
        function changeTemplateType() {
            var template_types = BX.message('bxmaker_smsnotice_template_type');

            if (template_type.val() != '0') {
                send_box.find('.btn_clean').hide();
               // result_msg_text.show();
                textarea_box.hide();
                fileds_box.empty().show();
                if (!!template_types[template_type.val()]) {
                    var type = template_types[template_type.val()];

                    textarea.val(type.TEXT);//.prop('disabled', true);

                    var fields = type.TYPEDESCR.split(/\n/);
                    var title = '';
                    var name = ''
                    for (var i in fields) {
                        if (name = fields[i].match(/#[\w\d\-.\{\}]+#/)) {
                            if (name[0] == '#PHONE#') continue;

                            title = fields[i].replace(name[0], '').replace(/^[ \-]+/, '').replace(/[ \-]+$/, '');

                            // добавляем товалько поля которые есть в тексте
                            if (type.TEXT.match(new RegExp(name[0], 'g'))) {
                                fileds_box.append('<div class="row_item"><small>' + fields[i] + '</small>' +
                                '<input type="text" name="' + name[0] + '" value="' + (!!type.TYPEVALUE[name[0]] ? type.TYPEVALUE[name[0]] : '') + '" placeholder=""/>' +
                                '</div>');
                            }

                        }
                    }
                }
            }
            else {
               // result_msg_text.hide();
                fileds_box.empty().hide();
                textarea_box.show();
                textarea.val('');//.prop('disabled', false);
                send_box.find('.btn_clean').show();
            }
            changeText();
        }

        // текст с автозаменой
        function getRealText() {
            var text = textarea.val();

            //если шаблон используется
            if (template_type.val() != '0') {
                fileds_box.find('input').each(function () {
                    var inp = $(this);
                    text = text.replace(new RegExp(inp.attr('name'), 'g'), inp.val().trim());
                });
            }

            //транслит
            if (btnTranslit.is(":checked")) {

                var trasObj = BX.message('bxmaker_smsnotice_translit');
                var newText = '';
                var char = '';
                for (var i = 0; i < text.length; i++) {

                    // Если символ найден в массиве то меняем его
                    if (trasObj[text[i].toLowerCase()] != undefined) {
                        char = trasObj[text[i].toLowerCase()];
                        if(text[i] != text[i].toLowerCase())
                        {
                            char = char.charAt(0).toUpperCase() + char.substr(1)
                        }
                        newText += char;
                    }
                    // Если нет, то оставляем так как есть
                    else {
                        newText += text[i];
                    }
                }
                text = newText;
            }

            text = text.replace(/^\s+/, '').replace(/\s+$/, '');

            return text;
        }


        function countSmsSize(text) {
            var l = 0;
            info_box.find('.text_size .count').text(text.length);

            if (btnTranslit.is(":checked")) {
                l = Math.floor(text.length / 160) + (text.length % 160 > 0 ? 1 : 0);
                info_box.find('.text_size .limit').text(160);
            }
            else
            {
                l = Math.floor(text.length / 70) + (text.length % 70 > 0 ? 1 : 0);
                info_box.find('.text_size .limit').text(70);
            }
            info_box.find('.text_size .sms').text( l > 0 ? l : 1 );
        }

        function showSmsTextResult(text) {
            result_msg_text.find('.text_box').html(text.replace(/\n/g, '<br>'));
        }

        function changeText() {
            var text = getRealText();
            countSmsSize(text);
            showSmsTextResult(text);

        }


        // спсиок сообщений
        var descr_error = $('.smsnotice_error_description_box');
        list_page.on("click", '.sms_comment_box > span', function () {

            descr_error.find('.descr').html('<pre>' + $(this).parent().find('.more').html() + '</pre>');
            descr_error.css({
                width: list_page.width() - 30
            }).fadeIn(500);

        });

        //ошибка
        descr_error.find('.close').on("click", function () {
            descr_error.fadeOut(500);
        });


    }


    // edit sms template
    $('form[name="bxmaker_smsnotice_template_edit_form"]').each(function () {
        var box = $(this);
        var field_box = box.find(".template_fields_box");
        var sel = box.find('select[name="TYPE_ID"]');

        sel.on("change", function () {

            field_box.empty();

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    sessid: BX.bitrix_sessid(),
                    method: 'getTemplateTypeFields',
                    type: sel.val()
                },
                error: function (r) {

                },
                success: function (r) {
                    if (r.status === 1) {
                        if (!!r.response.item && !!r.response.item.DESCR) {

                            field_box.html(r.response.item.DESCR.replace(/(#[\w\d]+#)+/mg, "<span>\$1</span>"));
                        }
                    }
                    else if (r.status === 0) {
                        field_box.html(r.error.error_msg);
                    }
                }
            })
        });

        field_box.on("click", function (e) {
            var el = $(e.target);
            if (el.is('span')) {
                box.find('textarea[name="TEXT"]').val(box.find('textarea[name="TEXT"]').val() + ' ' + el.text() + ' ');
            }
        });

        if (sel.val() != '')  sel.trigger("change");
    });

    //edit sms service
    $('form[name="bxmaker_smsnotice_service_edit_form"]').each(function () {
        var box = $(this);
        var field_box = box.find(".service_params_box");
        var sel = box.find('select[name="CODE"]');

        sel.on("change", function () {

            field_box.empty();

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    sessid: BX.bitrix_sessid(),
                    method: 'getServiceParams',
                    service: sel.val()
                },
                error: function (r) {

                },
                success: function (r) {
                    if (r.status === 1) {
                        if (!!r.response) {

                            var params = window.bxmaker_smsnotice_service_edit_params_current || {};
                            var item = null;
                            var fields = [];
                            var field_row = '<table>';

                            for (var s in r.response.items) {

                                item = r.response.items[s];

                                fields.push(s);

                                field_row += '<tr><td>' + item.NAME + '</td><td>';


                                switch (r.response.items[s]['TYPE']) {
                                    case 'LIST':
                                    {

                                        break;
                                    }
                                    case 'CHECKBOX':
                                    {
                                        field_row += '<input name="SERVICE_PARAMS[' + s + ']" value="Y" ' + (!!params[s] ? (params[s] == 'Y' ? ' checked="checked" ' : '') : (item.VALUE == 'Y' ? ' checked="checked" ' : '') ) + ' type="checkbox" /><br><small>' + (!!item.NAME_HINT ? item.NAME_HINT : '') + '</small>';
                                        break;
                                    }
                                    default:
                                    {
                                        field_row += '<input name="SERVICE_PARAMS[' + s + ']" value="' + (!!params[s] ? params[s] : item.VALUE ) + '" type="text" /><br><small>' + (!!item.NAME_HINT ? item.NAME_HINT : '') + '</small>';
                                    }
                                }

                                field_row += '</td></tr>';
                            }

                            field_row += '</table>';


                            field_box.append(r.response.description.replace(/#SERVICE_NOTICE_URL#/, ($('.service_notice_url').length ? $('.service_notice_url').text().replace(/\s+/, '') : '')) + field_row);

                            field_box.append('<input name="SERVICE_PARAMS_LIST" value="" type="hidden" />');
                            field_box.find('input[name="SERVICE_PARAMS_LIST"]').val(fields.join(','));

                        }

                    }
                    else if (r.status === 0) {
                        field_box.html(r.error.error_msg);
                    }
                }
            })
        });

        if (sel.val() != '')  sel.trigger("change");


    });

});