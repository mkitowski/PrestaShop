/**
 * @author Getresponse <grintegrations@getresponse.com>
 * @copyright GetResponse
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
function GrCustomFieldForm(
    pluginFields,
    grFields,
    selectedFields,
    controlRowId,
    tableBodyId,
    addButtonId
) {

    this.pluginFields = pluginFields;
    this.grFields = grFields;
    this.selectedFields = selectedFields;
    this.controlRow = $('#' + controlRowId);
    this.tableBodyId = tableBodyId;
    this.addButtonId = addButtonId;

    this.init = function() {

        $('#' + this.addButtonId).click(function() {
            this.addRow();
        }.bind(this));

        $.each(this.selectedFields, function(index, el) {
            this.addRow(el.customer_property_name, el.gr_custom_id);
        }.bind(this));

        this.rowColor();
    };

    this.addRow = function (selectedPluginField = null, selectedGrField = null) {

        var row = $('<tr>');

        // Plugin list
        var pluginList = $('<select/>', {'name' : 'plugin_fields[]'});

        for (var i in this.pluginFields) {
            pluginList.append($('<option/>').html(this.pluginFields[i]));
        }

        if (null !== selectedPluginField) {
            pluginList.val(selectedPluginField);
        }

        row.append($('<td>').append(pluginList));

        // Plugin list
        var grList = $('<select/>', {'name' : 'gr_fields[]'});
        for (var i in this.grFields) {
            grList.append($('<option/>').attr('value', this.grFields[i].id).html(this.grFields[i].name));
        }

        if (null !== selectedGrField) {
            grList.val(selectedGrField);
        }

        row.append($('<td>').append(grList));

        // Action
        var button = $('<span>', {'class':'btn btn-default'}).html('Remove');
        button.on('click', function (el) {
            this.removeRow($(el.target).parent().parent());
        }.bind(this));

        row.append($('<td>', {'class' :'text-center'}).append(button));

        this.controlRow.before(row);
        this.rowColor();
    };

    this.removeRow = function(el) {
        el.remove();
        this.rowColor();
    };

    this.rowColor = function () {
        $.each(
            $('#' + this.tableBodyId + ' tr'),
            function(index, el) {
                if (0 == index % 2) { $(el).addClass('odd') }
            }
        );
    }
}