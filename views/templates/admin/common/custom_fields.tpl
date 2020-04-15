{*
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     Getresponse <grintegrations@getresponse.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
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


