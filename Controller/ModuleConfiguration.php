<?php

namespace MarvinPoehls\AddressValidation\Controller;

use MarvinPoehls\AddressValidator\Model\Address;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class ModuleConfiguration extends ModuleConfiguration_parent
{
    protected $verificationHeaders = ["PLZ", "City", "Country", "Country-Shortcut"];
    protected $fileHeaders;
    protected $databaseColumns = null;
    protected $invalidFileError = false;
    protected $invalidHeadersError = false;
    protected $uploadComplete = false;

    public function saveConfVars()
    {
        parent::saveConfVars();

        $this->invalidFileError = false;
        $this->invalidHeadersError = false;
        $this->uploadComplete = false;

        if (Registry::getRequest()->getRequestParameter('oxid') === "addressvalidation") {
            $file = Registry::getConfig()->getUploadedFile("addressFile");

            if ($file["type"] === "text/csv") {
                $csvFile = fopen($file["tmp_name"],"r");
                $this->fileHeaders = fgetcsv($csvFile);

                if ($this->areHeadersValid($this->fileHeaders)) {
                    $this->databaseColumns = $this->getDatabaseColumns($this->fileHeaders);

                    $this->handleCsvFile($csvFile);

                    $this->uploadComplete = true;
                    fclose($csvFile);
                    unlink($file["tmp_name"]);
                } else {
                    $this->invalidHeadersError = true;
                }
            } else {
                $this->invalidFileError = true;
            }
        }
    }

    protected function areHeadersValid($aHeaders): bool
    {
        return count(array_diff($aHeaders, $this->verificationHeaders)) == 0;
    }

    protected function getDatabaseColumns($headers)
    {
        $aReturn = [];
        foreach ($headers as $sColumn) {
            $aReturn[] = str_replace("-","_",strtolower($sColumn));
        }
        return $aReturn;
    }

    protected function handleCsvFile($oCsvFile)
    {
        $address = oxNew(Address::class);
        $addressIds = $address->getIds();

        foreach($this->getRowsAssoc($oCsvFile) as $row) {
            $addressIdPosition = array_search($row['id'], $addressIds);

            if ($addressIdPosition === false) {
                $address->insertCsvRow($row);
            } else {
                unset($addressIds[$addressIdPosition]);
            }
        }

        $address->deleteRows($addressIds);
    }

    protected function getRowsAssoc($csvFile)
    {
        $return = [];
        while ($row = fgetcsv($csvFile)) {
            foreach ($row as $key => $value) {
                $row[$this->databaseColumns[$key]] = utf8_encode($value);
                unset($row[$key]);
            }
            $row['id'] = md5($row['plz'].$row['city'].$row['country_shortcut']);

            $return[] = $row;
        }
        return $return;
    }

    public function getInvalidFileError()
    {
        return $this->invalidFileError;
    }

    public function getInvalidHeadersError()
    {
        return $this->invalidHeadersError;
    }

    public function getUploadComplete()
    {
        return $this->uploadComplete;
    }

    public function getFileHeaders(){
        return $this->fileHeaders;
    }

    public function getVerificationHeaders(){
        return $this->verificationHeaders;
    }
}