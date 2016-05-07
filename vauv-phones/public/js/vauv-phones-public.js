;
(function ($) {
    'use strict';

    var Phones = {

        init: function (settings) {

            Phones.organizations = settings.organizations;
            Phones.departments = settings.departments;
            Phones.subscribers = settings.subscribers;
            Phones.search_bar = settings.search_bar;
            Phones.search = settings.search;
            Phones.breadcrumbs = settings.breadcrumbs;

            Phones.bindEvents();

            Phones.get_organizations();
        },
        bindEvents: function () {

            Phones.organizations.on('click', 'td', function () {
                Phones.get_phonebook($(this).data('organization'));
            });

            Phones.search_bar.on('keyup', function () {
                var searchfor = Phones.search_bar.val();
                if (searchfor.length > 2) {
                    Phones.get_search(searchfor);
                } else if (searchfor.length == 0) {
                    Phones.get_organizations();
                }
            });

            Phones.breadcrumbs.on('click', 'button', function () {
                Phones.get_phonebook($(this).data('department_id'));
            });

            Phones.organizations.on('onOrganizationsShown', function () {
                Phones.departments.hide();
                Phones.subscribers.hide();
                Phones.search.hide();
                Phones.organizations.show();
                // изменить хлебные крошки
                var breadcrumbs = $('<div class="btn-group" role="group"></div>');
                $('<button type="button" class="btn btn-default"><span class="glyphicon glyphicon-phone-alt"></span></button>').data('department_id', 0).appendTo(breadcrumbs);
                $('<button type="button" class="btn btn-default" disabled="disabled">Организации</button>').data('department_id', 0).appendTo(breadcrumbs);
                Phones.breadcrumbs.empty().append(breadcrumbs);
                // очистить поле ввода поиска
                Phones.search_bar.val('');
            });

            Phones.departments.on('onPhonebookShown', function () {
                Phones.organizations.hide();
                Phones.search.hide();
                Phones.departments.show();
                Phones.subscribers.show();
                Phones.search_bar.val('');
            });

            Phones.search.on('onSearchShown', function () {
                Phones.departments.hide();
                Phones.subscribers.hide();
                Phones.organizations.hide();
                Phones.search.show();
                // изменить хлебные крошки
                var breadcrumbs = $('<div class="btn-group" role="group"></div>');
                $('<button type="button" class="btn btn-default"><span class="glyphicon glyphicon-phone-alt"></span></button>').data('department_id', 0).appendTo(breadcrumbs);
                $('<button type="button" class="btn btn-default" disabled="disabled">Результаты поиска</button>').data('department_id', 0).appendTo(breadcrumbs);
                Phones.breadcrumbs.empty().append(breadcrumbs);
            });
        },
        get_organizations: function () {

            $.ajax({
                url: ajax.url,
                method: 'post',
                data: {
                    action: 'phones',
                    method: 'organizations',
                    _ajax_nonce: ajax.nonce
                },
                success: function (data) {
                    var organizations = JSON.parse(data);
                    var table = $('<table class="table table-bordered"></table>');
                    for (var i = 3; i < (organizations.length + 3); i += 3) {
                        var row = $('<tr></tr>');
                        var r = 0;
                        r = i - 3;
                        if (organizations[r]) {
                            $('<td>' + organizations[r].text + '</td>').data('organization', organizations[r].id).appendTo(row);
                        } else {
                            $('<td></td>').appendTo(row);
                        }
                        r = i - 2;
                        if (organizations[r]) {
                            $('<td>' + organizations[r].text + '</td>').data('organization', organizations[r].id).appendTo(row);
                        } else {
                            $('<td></td>').appendTo(row);
                        }
                        r = i - 1;
                        if (organizations[r]) {
                            $('<td>' + organizations[r].text + '</td>').data('organization', organizations[r].id).appendTo(row);
                        } else {
                            $('<td></td>').appendTo(row);
                        }
                        table.append(row);
                    }
                    Phones.organizations.empty();
                    table.appendTo(Phones.organizations);

                    Phones.organizations.trigger('onOrganizationsShown');
                }
            })
        },
        get_phonebook: function (department_id) {

            if (department_id == 0) {
                Phones.get_organizations();
            } else {
                $.ajax({
                    url: ajax.url,
                    method: 'post',
                    data: {
                        action: 'phones',
                        method: 'phonebook',
                        param: department_id,
                        _ajax_nonce: ajax.nonce
                    },
                    success: function (data) {
                        var phonebook = JSON.parse(data);

                        /* отделы */
                        var departments = phonebook.departments.nodes;
                        departments.unshift({
                            text: '<span class="glyphicon glyphicon-arrow-left"></span>&nbsp;&nbsp;' + phonebook.departments.text,
                            color: "#fff",
                            backColor: "#777",
                            id: phonebook.departments.parent,
                        });
                        Phones.departments.treeview({
                            data: departments,
                            levels: 0,
                            expandIcon: 'glyphicon glyphicon-arrow-right',
                            onNodeSelected: function (event, data) {
                                Phones.get_phonebook(data.id);
                            }
                        });

                        /* сотрудники */
                        var subscribers = phonebook.subscribers;
                        var dom = $('<table class="table"></table>');
                        for (var i = 0; i < subscribers.length; i++) {
                            if (subscribers[i].name && subscribers[i].position) {
                                $('<tr><td>' + subscribers[i].name + '<span class="text-muted">, ' + subscribers[i].position + '</span></td><td style="width: 5em;">' + subscribers[i].phone + '</td></tr>').appendTo(dom);
                            } else if (subscribers[i].name) {
                                $('<tr><td>' + subscribers[i].name + '</td><td style="width: 5em;">' + subscribers[i].phone + '</td></tr>').appendTo(dom);
                            } else if (subscribers[i].position) {
                                $('<tr><td>' + subscribers[i].position + '</td><td style="width: 5em;">' + subscribers[i].phone + '</td></tr>').appendTo(dom);
                            }
                        }
                        Phones.subscribers.empty().append(dom);

                        /* хлебные крошки */
                        var breadcrumbs = phonebook.departments.parents;
                        var dom = $('<div class="btn-group" role="group"></div>');
                        $('<button type="button" class="btn btn-default"><span class="glyphicon glyphicon-phone-alt"></span></button>').data('department_id', 0).appendTo(dom);
                        for (var i = 0; i < breadcrumbs.length; i++) {
                            if (i < (breadcrumbs.length - 1 )) {
                                $('<button type="button" class="btn btn-default">' + breadcrumbs[i].text + '</button>').data('department_id', breadcrumbs[i].id).appendTo(dom);
                            } else {
                                $('<button type="button" class="btn btn-default" disabled="disabled">' + breadcrumbs[i].text + '</button>').data('department_id', breadcrumbs[i].id).appendTo(dom);
                            }
                        }
                        Phones.breadcrumbs.empty().append(dom);
                    }
                });

                Phones.departments.trigger('onPhonebookShown');
            }
        },
        get_search: function (searchfor) {

            $.ajax({
                url: ajax.url,
                method: 'post',
                data: {
                    action: 'phones',
                    method: 'search',
                    param: searchfor,
                    _ajax_nonce: ajax.nonce
                },
                success: function (data) {
                    var founded = JSON.parse(data);
                    var dom = $('<ul class="list-group"></ul>');
                    for (var i = 0; i < founded.length; i++) {
                        var item = $('<li class="list-group-item"></li>');
                        $('<p><span class="glyphicon glyphicon-phone-alt"></span>&nbsp;&nbsp;' + founded[i].phone + '</p>').appendTo(item);
                        if (founded[i].name && founded[i].position) {
                            $('<p><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;' + founded[i].name + '<span class="text-muted">, ' + founded[i].position + '</span></p>').appendTo(item);
                        } else if (founded[i].name) {
                            $('<p><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;' + founded[i].name + '</p>').appendTo(item);
                        } else if (founded[i].position) {
                            $('<p><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;' + founded[i].position + '</p>').appendTo(item);
                        }
                        var breadcrumbs = JSON.parse(founded[i].parents);
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
                    Phones.search.empty().append(dom);
                    Phones.search.trigger('onSearchShown');
                }
            });
        }
    };

    $(document).ready(function () {
            Phones.init({
                organizations: $('#organizations'),
                departments: $('#departments'),
                subscribers: $('#subscribers'),
                search_bar: $('#search_bar'),
                search: $('#search'),
                breadcrumbs: $('#breadcrumbs'),
            });
        }
    );
})(jQuery);