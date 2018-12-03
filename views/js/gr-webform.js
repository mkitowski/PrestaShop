/**
 * @author Getresponse <grintegrations@getresponse.com>
 * @copyright GetResponse
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
$(function () {

    //webform selector
    $('input[name="subscription"]').on('change', function () {
        if ($('input[name="subscription"]:checked').val() == 1) {
            $('#style').parent().parent().show();
            $('#position').parent().parent().show();
            $('#form').parent().parent().show();
        } else {
            $('#style').parent().parent().hide();
            $('#position').parent().parent().hide();
            $('#form').parent().parent().hide();
        }
    });

    if ($('input[name="subscription"]:checked').val() == 1) {
        $('#style').parent().parent().show();
        $('#position').parent().parent().show();
        $('#form').parent().parent().show();
    } else {
        $('#style').parent().parent().hide();
        $('#position').parent().parent().hide();
        $('#form').parent().parent().hide();
    }

});
