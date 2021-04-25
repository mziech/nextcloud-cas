/**
 * @copyright Copyright (c) 2019-2021 Marco Ziech <marco+nc@ziech.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

function CasAdmin($cas) {
    var $services = $cas.find("#cas-services"),
        groups = {},
        maxGroupId = 0;

    function updateUnrestrictedGroup() {
        var $div = $(this).parent();
        $('input:first-child', $div).prop('checked', $('input[name="groups"]:checked', $div).length === 0);
    }

    function createGroups(selected) {
        var $div = $('<div class="cas-service-groups"></div>');

        var id = 'cas-service-group-' + (maxGroupId++);
        $div.append($('<input type="checkbox" class="checkbox">')
            .attr('id', id)
            .prop('checked', selected.length === 0)
            .click(updateUnrestrictedGroup)
        );
        $div.append($('<label>')
            .attr('for', id)
            .text(t('cas', 'Unrestricted'))
        );
        $div.append($('<br>'));

        $.each(groups, function (gid, displayName) {
            var id = 'cas-service-group-' + (maxGroupId++);
            $div.append($('<input type="checkbox" name="groups" class="checkbox">')
                .attr('id', id)
                .attr('value', gid)
                .prop('checked', selected.indexOf(gid) >= 0)
                .click(updateUnrestrictedGroup)
            );
            $div.append($('<label>')
                .attr('for', id)
                .text(displayName)
            );
            $div.append($('<br>'));
        });
        return $div;
    }

    function createLine(service) {
        return $('<tr>')
            .append($('<td>').append($('<input>').attr('name', 'id').val(service.id)))
            .append($('<td>').append($('<input>').attr('name', 'url').val(service.url)))
            .append($('<td>').append($('<select>').attr('name', 'urlMatchType')
                .append($('<option>').attr('value', 'PREFIX').text(t('cas', 'Match prefix')))
                .append($('<option>').attr('value', 'EXACT').text(t('cas', 'Exact match')))
                .append($('<option>').attr('value', 'REGEX').text(t('cas', 'Regular Expression')))
                .val(service.urlMatchType || 'PREFIX')))
            .append($('<td>').append(createGroups(service.groups || [])))
            .append($('<td>').append($('<input>').attr('name', 'strict')
                .attr('type', 'checkbox').attr('value', 'true').prop('checked', service.strict)))
            .append($('<td>').append('<button class="icon-delete js-cas-delete">&nbsp;</button>'))
            ;
    }

    $cas.find('#cas-add-service').click(function () {
        $services.append(createLine({}));
    });

    $cas.find('#cas-save').click(function () {
        var json = { services: [] };
        $services.find('tr').each(function () {
            var service = { groups: [] };
            $(this).find('input,select,textarea').each(function () {
                var $input = $(this),
                    name = $input.attr('name'),
                    value = $input.val();
                if ($input.is(':disabled') || ($input.attr('type') === 'checkbox' && !$input.is(':checked'))) {
                    return;
                }

                if ($.isArray(service[name])) {
                    service[name].push(value);
                } else {
                    service[name] = value;
                }
            });
            json.services.push(service);
        });

        $('#cas-clients input,select,button', $cas).prop('disabled', true);
        $('#cas-saving', $cas).show();

        $.ajax({
            method: 'POST',
            url: OC.generateUrl(OC.appswebroots['cas'] + '/admin'),
            data: JSON.stringify(json),
            contentType: 'application/json'
        }).always(function () {
            $('#cas-clients input,select,button', $cas).prop('disabled', false);
            $('#cas-saving', $cas).hide();
        });
    });

    $cas.on('click', '.js-cas-delete', function () {
        $(this).closest('tr').remove();
    });

    $.getJSON(OC.generateUrl(OC.appswebroots['cas'] + '/admin')).done(function (data) {
        $services.empty();
        groups = data.groups;
        $.each(data.services, function (i, service) {
            $services.append(createLine(service));
        });
        $('#cas-clients', $cas).show();
    }).always(function () {
        $('#cas-clients-loading', $cas).hide();
    });

}

$(function () {
    $('#cas').each(function () {
        new CasAdmin($(this));
    });
});
