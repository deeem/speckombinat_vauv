;
(function ($) {

    $(function () {

        $('#vauv_search').focus();

        $('#vauv_search').on('keyup', function () {
            var search = $('#vauv_search').val();
            if (search.length > 2) {
                $.ajax({
                    url: ajax.url,
                    type: 'post',
                    data: {
                        action: 'vauv_search',
                        param: search
                    },
                    success: function (data) {
                        show_search_result(JSON.parse(data));
                    }
                });
            } else if (search.length == 0) {
                $('#vauv_search_results').empty();
            }
        });

    });

    function show_search_result(data) {

        var search_results = $('#vauv_search_results');
        var phones = data.phones;
        var iplist = data.iplist;

        search_results.empty();

        // phones search result
        var dom = $('<ul class="list-group"></ul>');
        for (var i = 0; i < phones.length; i++) {
            var item = $('<li class="list-group-item"></li>');
            $('<p><span class="glyphicon glyphicon-phone-alt"></span>&nbsp;&nbsp;' + phones[i].phone + '</p>').appendTo(item);
            if (phones[i].name && phones[i].position) {
                $('<p><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;' + phones[i].name + '<span class="text-muted">, ' + phones[i].position + '</span></p>').appendTo(item);
            } else if (phones[i].name) {
                $('<p><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;' + phones[i].name + '</p>').appendTo(item);
            } else if (phones[i].position) {
                $('<p><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;' + phones[i].position + '</p>').appendTo(item);
            }
            var breadcrumbs = JSON.parse(phones[i].parents);
            var breadcrumbs_dom = '<p><span class="glyphicon glyphicon-map-marker"></span>&nbsp;&nbsp;<span>';
            for (var c = 0; c < breadcrumbs.length; c++) {
                breadcrumbs_dom += breadcrumbs[c].text;
                if (c < (breadcrumbs.length - 1))
                    breadcrumbs_dom += ' > ';
            }
            breadcrumbs_dom += '</span></p>';
            $(breadcrumbs_dom).appendTo(item);
            item.appendTo(dom);
        }
        $('<div class="col-md-6">').append(dom).appendTo(search_results);

        // iplist search result
        var table = $('<table class="table table-condensed">');
        for (var l = 0; l < iplist.length; l++) {
            var tr = '<tr>';
            tr += '<td style="width: 70px;">' + iplist[l].ip + '</td>';
            if (iplist[l].name != '' && iplist[l].user != '') {
                tr += '<td>' + iplist[l].name + '&nbsp;&nbsp;&nbsp;<span class="text-muted">&mdash;&nbsp;' + iplist[l].user + '</span>' + '</td>';
            } else if (iplist[l].name) {
                tr += '<td>' + iplist[l].name + '</td>';
            } else if (iplist[l].user) {
                tr += '<td><span class="text-muted">' + iplist[l].user + '</span></td>';
            } else {
                tr += '<td></td>';
            }
            tr += '<td style="width: 70px;">' + iplist[l].phone + '</td>';
            tr += '</tr>';
            $(tr).appendTo(table);
        }
        $('<div class="col-md-6 panel panel-default">').append(table).appendTo(search_results);
    }

})(jQuery);
