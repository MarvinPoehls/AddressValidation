<?php

namespace MarvinPoehls\AddressValidation\Controller;

use MarvinPoehls\AddressValidation\Model\Address;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Core\Registry;

class AddressValidator extends FrontendController
{
    public function validateAddress() {
        $countryId = Registry::getRequest()->getRequestParameter('countryId');
        $city = Registry::getRequest()->getRequestParameter('city');
        $zip = Registry::getRequest()->getRequestParameter('zip');

        $country = oxNew(Country::class);
        $country->loadInLang(0, $countryId);
        $countryName = $country->oxcountry__oxtitle->value;

        $address = oxNew(Address::class);
        echo $address->validateAddress($zip, $city, $countryName);

        exit();
    }
}