$(document).ready(function () {
    var isInitCalc = false;

    $('.calc-open-fancybox').fancybox({
        maxWidth    : 944,
        fitToView   : false,
        autoSize    : true,
        closeClick  : false,
        openEffect  : 'none',
        closeEffect : 'none',
        afterLoad : function() {
            initCalc();
        }
    });

    function initCalc()
    {
        clearResult();

        var isSelected = false;

        $.each(CALC_JSON.regions, function(i, region) {
            var option = {
                    value: region.id,
                    text: region.name
                };

            if (region.id == CALC_JSON.selected.region)
            {
                option.selected = true;
                isSelected = true;
                setRegion(region.name);
                setSections();
            }

            $('#calc-regions').append($("<option/>",  option));
        });

        if (!isSelected)
        {
            setRegion(CALC_JSON.regions[0].name);
            setSections();
        }

        function setSections() {
            $.each(CALC_JSON.sections, function(i, section) {
                var option = {
                        value: section.id,
                        text: section.name
                    };

                if (section.id == CALC_JSON.selected.section)
                {
                    option.selected = true;

                    if (section.brands.length)
                    {
                        setBrands(section);
                        $('#brand-container').show();
                    }
                    else
                    {
                        setItems(section, false);
                         $('#brand-container').hide();
                    }
                }

                $('#calc-sections').append($("<option/>",  option));
            });
        }


        function setItems(section, brand)
        {
            var is_selected = (CALC_JSON.selected.good) ? true : false;

            $.each(section.items, function(i, item) {
                var option = {
                        value: item.id,
                        text: item.name
                    };

                if (item.id == CALC_JSON.selected.good || !is_selected)
                {
                    is_selected = true;
                    CALC_JSON.selected.good = item.id;

                    option.selected = true;

                    setData(item);
                }

                if (brand)
                {
                    if (brand.id == item.brand)
                    {
                        $('#calc-goods').append($("<option/>",  option));
                    }
                }
                else
                {
                    $('#calc-goods').append($("<option/>",  option));
                }
            });
        }

        function setBrands(section)
        {
            var is_selected = (CALC_JSON.selected.brand) ? true : false;

            $.each(section.brands, function(i, brand) {
                var option = {
                        value: brand.id,
                        text: brand.name
                    };

                if (brand.id == CALC_JSON.selected.brand || !is_selected)
                {
                    is_selected = true;
                    CALC_JSON.selected.brand = brand.id;

                    option.selected = true;
                    setItems(section, brand);
                }

                $('#calc-brands').append($("<option/>",  option));
            });
        }

        function setData(item)
        {
            var date = new Date();
            var good = {};

            CALC_JSON.data = {
                'region': CALC_JSON.region
            }

            good.weight = (item.weight <= 0)? '0.4': item.weight;

            if (item.width > 0 && item.height > 0 && item.length > 0)
            {
                good.height = item.height;
                good.height = item.height;
                good.length = item.length;

                $('#calc-weight').html('Вес: <strong>' + item.weight + '</strong> кг Ширина: <strong>' + item.width + '</strong> м Высота: <strong>' + item.height + '</strong> м Длина: <strong>' + item.length + '</strong> м');
            }
            else
            {
                good.weight = item.weight;
                good.volume = '0.05';

                $('#calc-weight').html('Вес: <strong>' + item.weight + '</strong> кг Объем: <strong>0.05</strong> м3');
            }

            good.free = item.free;

            CALC_JSON.data.goods = [good];
        }

        function setRegion(name)
        {
            CALC_JSON.region = name;
        }

        function clearResult()
        {
            $('#calc-result').html('');
            $('#calc-sections option').remove();
            $('#calc-brands option').remove();
            $('#calc-goods option').remove();
        }


            $('#calc-regions').change(function() {
                var name = $(this).find('option:selected').text()
                setRegion(name);
                CALC_JSON.data.region = name;

                $('#calc-result').html('');
            });

            $('#calc-sections').change(function() {
                var id = $(this).find('option:selected').val();
                CALC_JSON.selected.section = id;
                CALC_JSON.selected.brand = 0;
                CALC_JSON.selected.good = 0;

                clearResult();
                setSections();
            });

            $('#calc-brands').change(function() {
                var id = $(this).find('option:selected').val();
                CALC_JSON.selected.brand = id;
                CALC_JSON.selected.good = 0;

                clearResult();
                setSections();
            });

            $('#calc-goods').change(function() {
                var id = $(this).find('option:selected').val();
                CALC_JSON.selected.good = id;

                clearResult();
                setSections();
            });

            $('#calc-button').click(function() {
                var button = $(this);
                button.attr('disabled', 'disabled');

                $.ajax({
                    url : '/bitrix/components/custom/calc-delivery.prototype/calculate.php',
                    jsonp : 'callback',
                    data : {
                        "json" : JSON.stringify(CALC_JSON.data)
                    },
                    type : 'GET',
                    dataType : 'jsonp',
                    success : function(data) {
                        console.log(data);
                        if(data.hasOwnProperty("result")) {
                            var html = '';

                            if (data.result.CALC_EXPRESS)
                            {
                                html += '<div class="calc-result-column">';
                                html += '<strong>Стандарт</strong><br>';
                            }

                            if (data.result.CALC_STANDART)
                            {
                                var price = (CALC_JSON.data.goods[0].free) ?
                                    '<strong>Бесплатно</strong>' :
                                    '<strong>' + data.result.CALC_STANDART + '</strong> руб.';

                                html += 'Цена доставки: ' + price + '<br>Срок доставки: <strong>' + data.result.STANDART.PERIODS + '</strong> дн.<br>';
                            }

                            if (data.result.CALC_EXPRESS)
                            {
                                var price = '<strong>' + data.result.CALC_EXPRESS + '</strong> руб.';
                                html += '</div><div class="calc-result-column">';
                                html += '<strong>Экспресс</strong><br>';
                                html += 'Цена доставки: ' + price + '<br>Срок доставки: <strong>' + data.result.EXPRESS.PERIODS + '</strong> дн.';
                                html += '</div>';
                            }

                            $('#calc-result').html(html);
                        } else {
                            $('#calc-result').html(data["error"]);
                        }

                        button.attr('disabled', false);
                    }
                });
                return false;
            });

            $('#calc-button-order').click(function() {
                var url = $(this).attr('data-url');

                $(location).attr('href', url + '&ID=' + CALC_JSON.selected.good);

                return false;
            });

        isInitCalc = true;
    }
});