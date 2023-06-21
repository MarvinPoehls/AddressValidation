<?php

namespace MarvinPoehls\AddressValidation\Controller;

use MarvinPoehls\AddressValidation\Model\Address;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use PHPUnit\Framework\Error\Error;

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
                $this->fileHeaders = fgetcsv($csvFile, null, ";");

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

    protected function getDatabaseColumns($headers): array
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
        $lineNumber = 1;

        $addressIds = $address->getIds();
        while($row = $this->getRow($oCsvFile)) {
            $addressIdPosition = array_search(trim($row[0], "'"), $addressIds);

            if ($addressIdPosition === false) {
                $address->saveInsert(implode(",",$row));
                if ($lineNumber == 1000) {
                    $address->sendInsert();
                    $lineNumber = 0;
                }
                $lineNumber++;
            } else {
                unset($addressIds[$addressIdPosition]);
            }
        }
        $address->sendInsert();

        $address->deleteRows($addressIds);
    }

    protected function getRow($csvFile)
    {
        $row = fgetcsv($csvFile, null, ";");
        if ($row) {
            for ($i = 0; $i < count($row); $i++) {
                while ($i == 0 && strlen($row[$i]) < 5){
                    $row[$i] = "0".$row[$i];
                }
                $row[$i] = utf8_encode("'".str_replace(","," ", $row[$i])."'");
            }
            $id = "'".str_replace("'", "",$row[3].$row[0].$row[1])."'";
            array_unshift($row, $id);
        }
        return $row;
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

    public function getFileHeaders(): string
    {
        return implode(",",$this->fileHeaders);
    }

    public function getVerificationHeaders(): string
    {
        return implode(",",$this->verificationHeaders);
    }
}