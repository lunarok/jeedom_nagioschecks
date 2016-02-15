
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
    tr += '<input class="cmdAttr" data-l1key="subtype" value="string" style="display:none;" />';
    tr += '</td>';
    tr += '<td>';
    tr += '<select class="cmdAttr" data-l1key="configuration" data-l2key="cron" style="height : 33px; width : 60%;display : inline-block;">';
    tr += '<option value="5">5 mn</option>';
    tr += '<option value="15">15 mn</option>';
    tr += '<option value="30">30 mn</option>';
    tr +='</select>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="notifalert" style="width : 80px;" placeholder="{{3 checks}}">';
    tr += '</td>';
    tr += '<td><span><input type="checkbox" class="cmdAttr bootstrapSwitch" data-size="mini" data-label-text="{{Par SSH}}" data-l1key="configuration" data-l2key="ssh"/></span>';
    tr += '<span><input type="checkbox" class="cmdAttr bootstrapSwitch" data-size="mini" data-label-text="{{Avec Sudo}}" data-l1key="configuration" data-l2key="sudo"/></span></td>';
    tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="check" style="width : 140px;" placeholder="{{Check}}"></td>';
    tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="options" style="width : 140px;" placeholder="{{Options}}"></td>';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="configuration" data-l2key="value"></span> : <span class="cmdAttr" data-l1key="configuration" data-l2key="status"></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr" data-l1key="eventOnly" checked style="display:none;" />';
    tr += '<span><input type="checkbox" class="cmdAttr bootstrapSwitch" data-l1key="isVisible" data-size="mini" data-label-text="{{Afficher}}" checked/></span> ';
    tr += '<span><input type="checkbox" class="cmdAttr bootstrapSwitch" data-l1key="isHistorized" data-size="mini" data-label-text="{{Historiser}}""/></span>';
    tr += '</td>';
    tr += '<td>';
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
