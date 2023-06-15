<?php

namespace MarvinPoehls\AddressValidation\Controller;

use MarvinPoehls\AddressValidator\Model\Address;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class ModuleConfiguration extends ModuleConfiguration_parent
{
    protected $verificationHeaders = ["PLZ", "City", "Country", "Country-Shortcut"];
    protected $databaseColumns = null;
    protected $invalidFileError = false;
    protected $invalidHeadersError = false;
    protected $uploadComplete = false;
    protected $arrayDiffrence;

    public function getDiffrence(){
        return $this->arrayDiffrence;
    }

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
                $headers = fgetcsv($csvFile);

                if ($this->areHeadersValid($headers)) {
                    $this->databaseColumns = $this->getDatabaseColumns($headers);

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
        $diffrence = array_diff($aHeaders, $this->verificationHeaders);
        if ($diffrence !== []) {
            $this->arrayDiffrence = $diffrence;
        }
        return $diffrence === [];
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
}