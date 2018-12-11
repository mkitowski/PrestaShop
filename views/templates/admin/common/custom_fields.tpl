<div class="panel col-lg-12" id="gr-custom-field-panel">
    <h3>Contacts Info</h3>

    <div class="table-responsive-row clearfix">
        <table id="table-AdminGetresponseExport" class="table AdminGetresponseExport">
            <thead>
                <tr class="nodrag nodrop">
                    <th>
                        <span class="title_box">Customer detail</span>
                    </th>
                    <th>
                        <span class="title_box">Custom fields in GetResponse</span>
                    </th>
                    <th class="text-center">
                        <span class="title_box">Action</span>
                    </th>
                </tr>
            </thead>

            <tbody id="gr-custom-field-table-body">

                {foreach from=$customs.defaults item=item}
                    <tr>
                        <td><select disabled><option>{$item.plugin_field}</option></select></td>
                        <td><select disabled><option>{$item.getresponse_field}</option></select></td>
                        <td class="text-center">---</td>
                    </tr>
                {/foreach}

                <tr id="gr-custom-field-control-row">
                    <td colspan="2"></td>
                    <td class="text-center">
                        <span class="btn btn-primary" id="gr-custom-field-add">Add</span>
                    </td>
                </tr>

            </tbody>

        </table>
    </div>
</div>

<script>


$(function() {

    var customFields = new GrCustomFieldForm(
        {$customs.plugin_field|@json_encode},
        {$customs.getresponse_field|@json_encode},
        {$customs.selected|@json_encode},
        'gr-custom-field-control-row',
        'gr-custom-field-table-body',
        'gr-custom-field-add'
    );

    customFields.init();

});

</script>


