<?php

namespace MarvinPoehls\AddressValidation\Controller;

use OxidEsales\Eshop\Core\Registry;

class ModuleConfiguration extends ModuleConfiguration_parent
{
    protected bool $adressValidatorUploadError = false;

    protected $sAddressValidatorCustomUploadError = false;

    public function saveConfVars()
    {
        parent::saveConfVars();
        $sModuleId = $this->_getModuleForConfigVars();
        if ($sModuleId == "module:adressvalidation") {
            $blReturn = $this->adressValidatorHandleFileUploads();
            if ($blReturn === false) {
                $this->adressValidatorUploadError = true;
            }
        }
    }

    protected function adressValidatorHandleFileUploads(): bool
    {
        if (!empty($_FILES)) {
            foreach ($_FILES as $sConfVar => $aFileInfo) {
                if (!empty($aFileInfo['name']) && $aFileInfo['error'] == 0) {
                    try {
                        $this->addressValidatorCleanUploadFileName($sConfVar);
                        $sReturn = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFile($sConfVar, 'modules/marvinpoehls/addressvalidator/out/img');
                    } catch(\Exception $exc) {
                        $this->sAddressValidatorCustomUploadError = $exc->getMessage();
                        $sReturn = false;
                    }
                    if ($sReturn === false) {
                        return false; // Upload error?
                    }

                    Registry::getConfig()->saveShopConfVar('str', $sConfVar, $sReturn, null, $this->_getModuleForConfigVars());
                }
            }
        }
        return true;
    }

    protected function addressValidatorCleanUploadFileName($sConfVar)
    {
        $_FILES[$sConfVar]['name'] = preg_replace('/[^\-_a-z0-9\.]/i', '', $_FILES[$sConfVar]['name']);
    }
}