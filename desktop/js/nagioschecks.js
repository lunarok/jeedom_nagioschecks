
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */
 $('#bt_selectMailCmd').on('click', function () {
     jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'message'}}, function (result) {
         $('.eqLogicAttr[data-l2key=alert]').atCaret('insert', result.human);
     });
 });

 $("#table_cmd").delegate(".listEquipementAction", 'click', function () {
     var el = $(this);
     jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
         var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.attr('data-input') + ']');
         calcul.atCaret('insert', result.human);
     });
 });

$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

 function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td><span class="cmdAttr" data-l1key="id"></span></td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
    tr += '<input class="cmdAttr" data-l1key="type" value="info" style="display:none;" />';
    tr += '<input class="cmdAttr" data-l1key="subtype" value="binary" style="display:none;" />';
    tr += '</td>';
    tr += '<td>';
    tr += '<select class="cmdAttr" data-l1key="configuration" data-l2key="cron" style="height : 33px; width : 90%;display : inline-block;">';
    tr += '<option value="5">5 mn</option>';
    tr += '<option value="15">15 mn</option>';
    tr += '<option value="30">30 mn</option>';
    tr +='</select>';
    tr += '<td>';
    tr += 'Essais : ';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="notifalert" style="width : 80px;" placeholder="{{3 checks}}">';
    tr += '<br>';
    tr += 'Action : ';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="cmdalert" style="width : 140px;">';
    tr += '<a class="btn btn-default btn-sm cursor listEquipementAction" data-input="cmdalert" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>';
    tr += '</td>';
    tr += '<td>';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="configuration" data-l2key="ssh" />{{Par SSH}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="configuration" data-l2key="sudo" />{{Avec Sudo}}</label></span> ';
    tr += '<td>Check : ';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="check" style="width : 200px;" placeholder="{{Check}}">';
    tr += '<br>Options : ';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="options" style="width : 200px;" placeholder="{{Options}}">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="configuration" data-l2key="code"></span> : <span class="cmdAttr" data-l1key="configuration" data-l2key="status"></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
    tr += '</td>';
    tr += '<td>';
    tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
    }
