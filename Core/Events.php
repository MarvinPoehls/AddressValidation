<?php

namespace MarvinPoehls\AddressValidation\Core;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\DbMetaDataHandler;

class Events
{
    public static function onActivate(){
        self::addFields();
        self::regenerateViews();
    }

    public static function addFields(){
        self::addTableIfNotExists('fcaddresses');
        self::addColumnIfNotExists('fcaddresses', 'PLZ', "ALTER TABLE fcaddresses ADD PLZ VARCHAR(50) COLLATE 'utf8_general_ci'");
        self::addColumnIfNotExists('fcaddresses', 'CITY', "ALTER TABLE fcaddresses ADD PLZ VARCHAR(50) COLLATE 'utf8_general_ci'");
        self::addColumnIfNotExists('fcaddresses', 'COUNTRY', "ALTER TABLE fcaddresses ADD PLZ VARCHAR(50) COLLATE 'utf8_general_ci'");
        self::addColumnIfNotExists('fcaddresses', 'COUNTRYSHORTCUT', "ALTER TABLE fcaddresses ADD PLZ VARCHAR(50) COLLATE 'utf8_general_ci'");
    }

    protected static function regenerateViews(){
        $dbMetaDataHandler = oxnew(DbMetaDataHandler::class);
        $dbMetaDataHandler->updateViews();
    }

    protected static function addColumnIfNotExists($sTableName, $sColumnName, $sQuery, $aNewColumnDataQueries = array()): bool
    {
        $aColumns = DatabaseProvider::getDb()->getAll("SHOW COLUMNS FROM {$sTableName} LIKE ?", array($sColumnName));
        if (empty($aColumns)) {
            try {
                DatabaseProvider::getDb()->Execute($sQuery);
                foreach ($aNewColumnDataQueries as $sQuery) {
                    DatabaseProvider::getDb()->Execute($sQuery);
                }
                return true;
            } catch (\Exception $e) {
                // does nothing yet
            }
        }
        return false;
    }

    protected static function addTableIfNotExists($tableName)
    {
        $oDb = DatabaseProvider::getDb();

        if (count($oDb->getAll("SHOW TABLES LIKE '{$tableName}'")) === 0) {
            $oDb->execute("CREATE TABLE {$tableName} (`OXID` CHAR(32) NOT NULL COLLATE 'latin1_general_ci', PRIMARY KEY (`OXID`))");
        }
    }
}