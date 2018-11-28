/**
 * @author Getresponse <grintegrations@getresponse.com>
 * @copyright GetResponse
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
function ecommerceDisplay()
{
    if ($('#ecommerce_on').is(':checked')) {
        $('#shop').parent().parent().show();
        $('#list').parent().parent().show();
        $('#form-GREcommerce').show();
    } else {
        $('#shop').parent().parent().hide();
        $('#list').parent().parent().hide();
        $('#form-GREcommerce').hide();
    }
}

$(document).ready(function () {
    $('.prestashop-switch').click(function () {
        ecommerceDisplay();
    }).trigger('click');

    $('button[type="reset"]').click(function (e) {
       e.preventDefault();
       window.location.href = $('#back_url').val();
    });
});
