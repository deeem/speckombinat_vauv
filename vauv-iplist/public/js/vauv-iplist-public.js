(function ($) {
    'use strict';

    var IPlist = {

        init: function (settings) {

            IPlist.subnets = settings.subnets;
            IPlist.subnet_add = settings.subnet_add;
            IPlist.iplist = settings.iplist;
            IPlist.search_bar = settings.search_bar;
            IPlist.ipinfo = settings.ipinfo;
            IPlist.current_subnet = settings.default_subnet;

            IPlist.bindEvents();

            IPlist.get_subnets(IPlist.current_subnet);
        },
        bindEvents: function () {

            /* выбрали подсеть в селекте выбора подсети */
            IPlist.subnets.on('change', function () {

                if ($(this).val() == 'add') {
                    IPlist.subnet_add.modal('show');
                } else {
                    IPlist.current_subnet = $(this).val();
                    IPlist.get_iplist(IPlist.current_subnet);
                }
            });

            /* нажата добавить в форме добавления подсети */
            IPlist.subnet_add.on('click', 'button', function () {

                $.ajax({
                    url: ajax_object.ajaxurl,
                    type: 'post',
                    data: {
                        action: 'datatable',
                        method: 'add_subnet',
                        param: $('#new_subnet').val(),
                        _ajax_nonce: ajax_object.nonce
                    },
                    success: function (data) {
                        var message = JSON.parse(data);
                        if (message.error !== '') {
                            $('#new_message').text(message.error);
                        } else {
                            IPlist.subnet_add.modal('hide');
                            IPlist.get_subnets($('#new_subnet').val());
                        }
                    }
                });
            });

            /* нажатие на строке таблицы */
            IPlist.iplist.on('click', 'tr', function () {

                var info = $(this).data('info');
                // заполнить форму
                $('#iplist-form-ip').val(info.ip);
                $('#iplist-form-name').val(info.name);
                $('#iplist-form-user').val(info.user);
                $('#iplist-form-phone').val(info.phone);

                // показать форму
                IPlist.ipinfo.modal('show');
            });

            /* нажатие кнопки удалить на форме */
            IPlist.ipinfo.on('click', 'button[name="delete"]', function () {
                var new_values = {
                    ip: $('#iplist-form-ip').val(),
                    name: '',
                    user: '',
                    phone: ''
                };
                $.ajax({
                    url: ajax_object.ajaxurl,
                    type: 'post',
                    data: {
                        action: 'datatable',
                        method: 'delete',
                        param: new_values.ip,
                        _ajax_nonce: ajax_object.nonce
                    },
                    success: function (message) {
                        // обновить содержимое tr
                        IPlist.iplist.find('tr').each(function () {
                            var info = $(this).data('info');
                            if (info.ip == new_values.ip) {
                                $(this).data('info', new_values);
                                $(this).replaceWith($('<tr><td style="width: 70px;">' + new_values.ip + '</td>' +
                                    '<td>' + new_values.name + '&nbsp;&nbsp;&nbsp;<span class="text-muted">&mdash;&nbsp;' + new_values.user + '</span>' + '</td>' +
                                    '<td style="width: 70px;">' + new_values.phone + '</td></tr>'
                                ).data('info', new_values));
                            }
                        });
                        // скрыть форму
                        IPlist.ipinfo.trigger('onIPinfoHide');
                        // если в ответе сообщают что это был последний элемент, то выбрать другую подсеть
                        console.log(message);
                        if (message == 'subnet deleted') {
                            IPlist.get_subnets();
                        }
                    }
                });

            });

            /* нажата кнопка сохранить на форме */
            IPlist.ipinfo.on('click', 'button[name="save"]', function () {
                var new_values = {
                    ip: $('#iplist-form-ip').val(),
                    name: $('#iplist-form-name').val(),
                    user: $('#iplist-form-user').val(),
                    phone: $('#iplist-form-phone').val()
                };
                $.ajax({
                    url: ajax_object.ajaxurl,
                    type: 'post',
                    data: {
                        action: 'datatable',
                        method: 'save',
                        param: {
                            ip: new_values.ip,
                            data: {
                                name: new_values.name,
                                user: new_values.user,
                                phone: new_values.phone
                            }
                        },
                        _ajax_nonce: ajax_object.nonce
                    },
                    success: function () {
                        // обновить содержимое tr
                        IPlist.iplist.find('tr').each(function () {
                            var info = $(this).data('info');
                            if (info.ip == new_values.ip) {
                                $(this).data('info', new_values);
                                $(this).replaceWith($('<tr><td style="width: 70px;">' + new_values.ip + '</td>' +
                                    '<td>' + new_values.name + '&nbsp;&nbsp;&nbsp;<span class="text-muted">&mdash;&nbsp;' + new_values.user + '</span>' + '</td>' +
                                    '<td style="width: 70px;">' + new_values.phone + '</td></tr>'
                                ).data('info', new_values));
                            }
                        });
                        IPlist.ipinfo.trigger('onIPinfoHide');
                    }
                });
            });

            /* поиск */
            IPlist.search_bar.on('keyup', function () {
                var searchfor = IPlist.search_bar.val();
                if (searchfor.length > 2) {
                    IPlist.subnets.hide();
                    IPlist.get_search(searchfor);
                } else if (searchfor.length == 0) {
                    IPlist.get_subnets(IPlist.current_subnet);
                    IPlist.subnets.show();
                }
            });

            /* скрытие формы */
            IPlist.ipinfo.on('onIPinfoHide', function () {
                IPlist.ipinfo.find('input').each(function () {
                    $(this).val('');
                });
                IPlist.ipinfo.modal('hide');
            });
        },
        get_subnets: function (subnet) {
            $.ajax({
                url: ajax_object.ajaxurl,
                type: 'post',
                data: {
                    action: 'datatable',
                    method: 'subnets',
                    _ajax_nonce: ajax_object.nonce
                },
                success: function (data) {
                    var subnets = JSON.parse(data);
                    IPlist.subnets.empty();
                    for (var i = 0; i < subnets.length; i++) {
                        $('<option value="' + subnets[i] + '">' + subnets[i] + '</option>').appendTo(IPlist.subnets);
                    }
                    $('<option value="add">+ добавить</option>').appendTo(IPlist.subnets);

                    // показать список адресов переданной подсети или самой первой в списке
                    if (subnet) {
                        IPlist.subnets.val(subnet);
                        IPlist.get_iplist(subnet);
                    } else {
                        IPlist.subnets.val(subnets[0]);
                        IPlist.get_iplist(subnets[0]);
                    }
                }
            });
        },
        get_iplist: function (range) {
            $.ajax({
                url: ajax_object.ajaxurl,
                type: 'post',
                data: {
                    action: 'datatable',
                    method: 'list',
                    param: range,
                    _ajax_nonce: ajax_object.nonce
                },
                success: function (data) {
                    IPlist.generate_iplist(JSON.parse(data));
                }
            });
        },
        get_search: function (searchfor) {
            $.ajax({
                url: ajax_object.ajaxurl,
                type: 'post',
                data: {
                    action: 'datatable',
                    method: 'search',
                    param: searchfor,
                    _ajax_nonce: ajax_object.nonce
                },
                success: function (data) {
                    IPlist.generate_iplist(JSON.parse(data));
                }
            });
        },
        generate_iplist: function (data) {

            var left_table = $('<table class="table table-condensed table-hover">');
            for (var l = 0; l < (parseInt(data.length / 2)); l++) {
                var tr = '<tr>';
                tr += '<td style="width: 70px;">' + data[l].ip + '</td>';
                if (data[l].name != '' && data[l].user != '') {
                    tr += '<td>' + data[l].name + '&nbsp;&nbsp;&nbsp;<span class="text-muted">&mdash;&nbsp;' + data[l].user + '</span>' + '</td>';
                } else if (data[l].name) {
                    tr += '<td>' + data[l].name + '</td>';
                } else if (data[l].user) {
                    tr += '<td><span class="text-muted">' + data[l].user + '</span></td>';
                } else {
                    tr += '<td></td>';
                }
                tr += '<td style="width: 70px;">' + data[l].phone + '</td>';
                tr += '</tr>';
                $(tr).data('info', data[l]).appendTo(left_table);
            }
            var right_table = $('<table class="table table-condensed table-hover">');
            for (var r = parseInt(data.length / 2); r < data.length; r++) {
                var tr = '<tr>';
                tr += '<td style="width: 70px;">' + data[r].ip + '</td>';
                if (data[r].name != '' && data[r].user != '') {
                    tr += '<td>' + data[r].name + '&nbsp;&nbsp;&nbsp;<span class="text-muted">&mdash;&nbsp;' + data[r].user + '</span>' + '</td>';
                } else if (data[r].name) {
                    tr += '<td>' + data[r].name + '</td>';
                } else if (data[r].user) {
                    tr += '<td><span class="text-muted">' + data[r].user + '</span></td>';
                } else {
                    tr += '<td></td>';
                }
                tr += '<td style="width: 70px;">' + data[r].phone + '</td>';
                tr += '</tr>';
                $(tr).data('info', data[r]).appendTo(right_table);
            }
            IPlist.iplist.empty();
            $('<div class="col-md-6">').append(left_table).appendTo(IPlist.iplist);
            $('<div class="col-md-6">').append(right_table).appendTo(IPlist.iplist);
        }
    };

    $(document).ready(function () {
        IPlist.init({
            default_subnet: '10.0.8',
            subnets: $('#subnets'),
            subnet_add: $('#subnet-modal'),
            search_bar: $('#search_bar'),
            iplist: $('#iplist'),
            ipinfo: $('#iplist-modal'),
        });
    });

})(jQuery);