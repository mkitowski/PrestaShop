<?php
namespace GetResponse\Hook;

use Tools;
use Category;
use Product;
use Link;
use Order;
use OrderState;
use Address;
use Country;
use PrestaShopException;
use GrShareCode\Product\ProductsCollection as GrProductsCollection;
use GrShareCode\Product\Variant\Images\ImagesCollection as GrImagesCollection;
use GrShareCode\Product\Category\CategoryCollection as GrCategoryCollection;
use GrShareCode\Product\Product as GrProduct;
use GrShareCode\Product\Category\Category as GrCategory;
use GrShareCode\Product\Variant\Images\Image as GrImage;
use GrShareCode\Product\Variant\Variant as GrVariant;
use GrShareCode\Address\Address as GrAddress;
use GrShareCode\CountryCodeConverter as GrCountryCodeConverter;

/**
 * Class Hook
 * @package GetResponse\Hook
 */
class Hook
{

    /**
     * @param Order $order
     * @return GrProductsCollection
     */
    /**
     * @param Order $order
     * @return GrProductsCollection
     * @throws PrestaShopException
     */
    protected function getOrderProductsCollection(Order $order)
    {
        $productsCollection = new GrProductsCollection();
        $products = $order->getProducts();

        foreach ($products as $product) {
            $productsCollection->add($this->createGrProductObject($product));
        }

        return $productsCollection;
    }

    /**
     * @param string $countryCode Two letters country code
     * @return string|bool Three letters country code
     */
    protected function convertCountryCode($countryCode)
    {
        $iso31661 = array(
            'AF' => 'AFG',
            'AX' => 'ALA',
            'AL' => 'ALB',
            'DZ' => 'DZA',
            'AS' => 'ASM',
            'AD' => 'AND',
            'AO' => 'AGO',
            'AI' => 'AIA',
            'AQ' => 'ATA',
            'AG' => 'ATG',
            'AR' => 'ARG',
            'AM' => 'ARM',
            'AW' => 'ABW',
            'AU' => 'AUS',
            'AT' => 'AUT',
            'AZ' => 'AZE',
            'BS' => 'BHS',
            'BH' => 'BHR',
            'BD' => 'BGD',
            'BB' => 'BRB',
            'BY' => 'BLR',
            'BE' => 'BEL',
            'BZ' => 'BLZ',
            'BJ' => 'BEN',
            'BM' => 'BMU',
            'BT' => 'BTN',
            'BO' => 'BOL',
            'BQ' => 'BES',
            'BA' => 'BIH',
            'BW' => 'BWA',
            'BV' => 'BVT',
            'BR' => 'BRA',
            'IO' => 'IOT',
            'BN' => 'BRN',
            'BG' => 'BGR',
            'BF' => 'BFA',
            'BI' => 'BDI',
            'KH' => 'KHM',
            'CM' => 'CMR',
            'CA' => 'CAN',
            'CV' => 'CPV',
            'KY' => 'CYM',
            'CF' => 'CAF',
            'TD' => 'TCD',
            'CL' => 'CHL',
            'CN' => 'CHN',
            'CX' => 'CXR',
            'CC' => 'CCK',
            'CO' => 'COL',
            'KM' => 'COM',
            'CG' => 'COG',
            'CD' => 'COD',
            'CK' => 'COK',
            'CR' => 'CRI',
            'CI' => 'CIV',
            'HR' => 'HRV',
            'CU' => 'CUB',
            'CW' => 'CUW',
            'CY' => 'CYP',
            'CZ' => 'CZE',
            'DK' => 'DNK',
            'DJ' => 'DJI',
            'DM' => 'DMA',
            'DO' => 'DOM',
            'EC' => 'ECU',
            'EG' => 'EGY',
            'SV' => 'SLV',
            'GQ' => 'GNQ',
            'ER' => 'ERI',
            'EE' => 'EST',
            'ET' => 'ETH',
            'FK' => 'FLK',
            'FO' => 'FRO',
            'FJ' => 'FIJ',
            'FI' => 'FIN',
            'FR' => 'FRA',
            'GF' => 'GUF',
            'PF' => 'PYF',
            'TF' => 'ATF',
            'GA' => 'GAB',
            'GM' => 'GMB',
            'GE' => 'GEO',
            'DE' => 'DEU',
            'GH' => 'GHA',
            'GI' => 'GIB',
            'GR' => 'GRC',
            'GL' => 'GRL',
            'GD' => 'GRD',
            'GP' => 'GLP',
            'GU' => 'GUM',
            'GT' => 'GTM',
            'GG' => 'GGY',
            'GN' => 'GIN',
            'GW' => 'GNB',
            'GY' => 'GUY',
            'HT' => 'HTI',
            'HM' => 'HMD',
            'VA' => 'VAT',
            'HN' => 'HND',
            'HK' => 'HKG',
            'HU' => 'HUN',
            'IS' => 'ISL',
            'IN' => 'IND',
            'ID' => 'IDN',
            'IR' => 'IRN',
            'IQ' => 'IRQ',
            'IE' => 'IRL',
            'IM' => 'IMN',
            'IL' => 'ISR',
            'IT' => 'ITA',
            'JM' => 'JAM',
            'JP' => 'JPN',
            'JE' => 'JEY',
            'JO' => 'JOR',
            'KZ' => 'KAZ',
            'KE' => 'KEN',
            'KI' => 'KIR',
            'KP' => 'PRK',
            'KR' => 'KOR',
            'KW' => 'KWT',
            'KG' => 'KGZ',
            'LA' => 'LAO',
            'LV' => 'LVA',
            'LB' => 'LBN',
            'LS' => 'LSO',
            'LR' => 'LBR',
            'LY' => 'LBY',
            'LI' => 'LIE',
            'LT' => 'LTU',
            'LU' => 'LUX',
            'MO' => 'MAC',
            'MK' => 'MKD',
            'MG' => 'MDG',
            'MW' => 'MWI',
            'MY' => 'MYS',
            'MV' => 'MDV',
            'ML' => 'MLI',
            'MT' => 'MLT',
            'MH' => 'MHL',
            'MQ' => 'MTQ',
            'MR' => 'MRT',
            'MU' => 'MUS',
            'YT' => 'MYT',
            'MX' => 'MEX',
            'FM' => 'FSM',
            'MD' => 'MDA',
            'MC' => 'MCO',
            'MN' => 'MNG',
            'ME' => 'MNE',
            'MS' => 'MSR',
            'MA' => 'MAR',
            'MZ' => 'MOZ',
            'MM' => 'MMR',
            'NA' => 'NAM',
            'NR' => 'NRU',
            'NP' => 'NPL',
            'NL' => 'NLD',
            'AN' => 'ANT',
            'NC' => 'NCL',
            'NZ' => 'NZL',
            'NI' => 'NIC',
            'NE' => 'NER',
            'NG' => 'NGA',
            'NU' => 'NIU',
            'NF' => 'NFK',
            'MP' => 'MNP',
            'NO' => 'NOR',
            'OM' => 'OMN',
            'PK' => 'PAK',
            'PW' => 'PLW',
            'PS' => 'PSE',
            'PA' => 'PAN',
            'PG' => 'PNG',
            'PY' => 'PRY',
            'PE' => 'PER',
            'PH' => 'PHL',
            'PN' => 'PCN',
            'PL' => 'POL',
            'PT' => 'PRT',
            'PR' => 'PRI',
            'QA' => 'QAT',
            'RE' => 'REU',
            'RO' => 'ROU',
            'RU' => 'RUS',
            'RW' => 'RWA',
            'BL' => 'BLM',
            'SH' => 'SHN',
            'KN' => 'KNA',
            'LC' => 'LCA',
            'MF' => 'MAF',
            'SX' => 'SXM',
            'PM' => 'SPM',
            'VC' => 'VCT',
            'WS' => 'WSM',
            'SM' => 'SMR',
            'ST' => 'STP',
            'SA' => 'SAU',
            'SN' => 'SEN',
            'RS' => 'SRB',
            'SC' => 'SYC',
            'SL' => 'SLE',
            'SG' => 'SGP',
            'SK' => 'SVK',
            'SI' => 'SVN',
            'SB' => 'SLB',
            'SO' => 'SOM',
            'ZA' => 'ZAF',
            'GS' => 'SGS',
            'SS' => 'SSD',
            'ES' => 'ESP',
            'LK' => 'LKA',
            'SD' => 'SDN',
            'SR' => 'SUR',
            'SJ' => 'SJM',
            'SZ' => 'SWZ',
            'SE' => 'SWE',
            'CH' => 'CHE',
            'SY' => 'SYR',
            'TW' => 'TWN',
            'TJ' => 'TJK',
            'TZ' => 'TZA',
            'TH' => 'THA',
            'TL' => 'TLS',
            'TG' => 'TGO',
            'TK' => 'TKL',
            'TO' => 'TON',
            'TT' => 'TTO',
            'TN' => 'TUN',
            'TR' => 'TUR',
            'TM' => 'TKM',
            'TC' => 'TCA',
            'TV' => 'TUV',
            'UG' => 'UGA',
            'UA' => 'UKR',
            'AE' => 'ARE',
            'GB' => 'GBR',
            'US' => 'USA',
            'UM' => 'UMI',
            'UY' => 'URY',
            'UZ' => 'UZB',
            'VU' => 'VUT',
            'VE' => 'VEN',
            'VN' => 'VNM',
            'VG' => 'VGB',
            'VI' => 'VIR',
            'WF' => 'WLF',
            'EH' => 'ESH',
            'YE' => 'YEM',
            'ZM' => 'ZMB',
            'ZW' => 'ZWE'
        );

        return isset($iso31661[$countryCode]) ? $iso31661[$countryCode] : false;
    }

    /**
     * @param Order $order
     * @return string
     */
    protected function getOrderStatus($order)
    {
        $status = (new OrderState((int)$order->getCurrentState(), $order->id_lang))->name;

        return empty($status) ? 'new' : $status;
    }

    /**
     * @param array $product
     * @return GrProduct
     * @throws PrestaShopException
     */
    protected function createGrProductObject($product)
    {
        $imagesCollection = new GrImagesCollection();
        $categoryCollection = new GrCategoryCollection();
        $coreProduct = new Product($product['id_product']);
        $categories = $coreProduct->getCategories();

        foreach ($coreProduct->getImages(null) as $image) {
            $imagePath = (new Link())->getImageLink($coreProduct->link_rewrite, $image['id_image'], 'home_default');
            $imagesCollection->add(new GrImage(Tools::getProtocol(Tools::usingSecureMode()) . $imagePath, (int)$image['position']));
        }

        foreach ($categories as $category) {
            $coreCategory = new Category($category);
            $categoryCollection->add(new GrCategory($coreCategory->getName()));
        }

        $grVariant = new GrVariant(
            (int)$product['id_product'],
            $this->normalizeToString($coreProduct->name),
            $coreProduct->getPrice(false),
            $coreProduct->getPrice(),
            $product['reference']
        );
        //@TODO: pobrac ilosc
        $grVariant->setQuantity(100);
        $grVariant->setImages($imagesCollection);
        $grVariant->setUrl((new Link())->getProductLink($coreProduct));

        return new GrProduct(
            (int)$product['id_product'],
            $this->normalizeToString($coreProduct->name),
            $grVariant,
            $categoryCollection
        );
    }


    /**
     * @param Order $order
     * @return GrAddress
     */
    protected function getOrderShippingAddress(Order $order)
    {
        $address = new Address($order->id_address_delivery);
        $country = new Country($address->id_country);
        return new GrAddress(
            GrCountryCodeConverter::getCountryCodeInISO3166Alpha3($country->iso_code),
            $this->normalizeToString($country->name)
        );
    }

    /**
     * @param Order $order
     * @return GrAddress
     */
    protected function getOrderBillingAddress(Order $order)
    {
        $address = new Address($order->id_address_invoice);
        $country = new Country($address->id_country);
        return new GrAddress(
            GrCountryCodeConverter::getCountryCodeInISO3166Alpha3($country->iso_code),
            $this->normalizeToString($country->name)
        );
    }

    /**
     * @param string $text
     * @return mixed
     */
    private function normalizeToString($text)
    {
        return is_array($text) ? reset($text) : $text;
    }
}