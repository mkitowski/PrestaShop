<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Unit/PrestashopMock/Translate.php';
require_once __DIR__ . '/Unit/PrestashopMock/Link.php';
require_once __DIR__ . '/Unit/PrestashopMock/Category.php';
require_once __DIR__ . '/Unit/PrestashopMock/Tools.php';
require_once __DIR__ . '/Unit/PrestashopMock/Product.php';
require_once __DIR__ . '/Unit/PrestashopMock/ProductGenerator.php';
require_once __DIR__ . '/Unit/PrestashopMock/Cart.php';
require_once __DIR__ . '/Unit/PrestashopMock/Customer.php';
require_once __DIR__ . '/Unit/PrestashopMock/MockParams/CustomerParams.php';
require_once __DIR__ . '/Unit/PrestashopMock/Currency.php';
require_once __DIR__ . '/Unit/PrestashopMock/Order.php';
require_once __DIR__ . '/Unit/PrestashopMock/OrderState.php';
require_once __DIR__ . '/Unit/PrestashopMock/Address.php';
require_once __DIR__ . '/Unit/PrestashopMock/Country.php';
require_once __DIR__ . '/Unit/PrestashopMock/PrestaShopDatabaseException.php';
require_once __DIR__ . '/../classes/GetResponseRepository.php';
require_once __DIR__ . '/../classes/GrApiException.php';

define('__PS_BASE_URI__', 'http://my-prestashop.com/');