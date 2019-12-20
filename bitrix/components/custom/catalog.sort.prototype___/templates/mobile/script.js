$(function(){

    function merge(args) {
        args  = Array.prototype.slice.call(arguments);
        var o = { };
        for(var i = 0; i < args.length; ++i)
            for(var j = 0; j < args[i].length; ++j)
                o[args[i][j].name] = args[i][j].value;
        return o;
    }

    function expand(o) {
        var a = [ ];
        for(var p in o)
            if(o.hasOwnProperty(p))
                a.push({ name: p, value: o[p]});
        return a;
    }

    function addUrlParams(urlParams) {
        var result = '',
            curParams = [],
            url = location.href.split('#')[0].split('?'),
            base = url[0],
            query = url[1],
            params = (query) ? query.split('&') : [];

        $.each(params, function(index, param) {
            var param = param.split('=');
            curParams.push({name: param[0], value: param[1]});
        });

        params = expand(merge(curParams, urlParams));

        $.each(params, function(index, param) {
            result += (result) ? '&' : '';
            result += param.name + '=' + param.value;
        });

        $(location).attr('href', base + '?' + result);
        return false;
    }

    $('#catalog-sort select').change(function() {
        var urlParams = [];
        $('#catalog-sort select').each(function() {
            var name = $(this).attr('name'),
                value = $(this).val();

            urlParams.push({name:name, value:value});
        });

        addUrlParams(urlParams);
    });
});