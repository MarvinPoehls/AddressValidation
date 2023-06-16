<?php

namespace MarvinPoehls\AddressValidation\Core\Events;

use OxidEsales\Eshop\Core\Base;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\DbMetaDataHandler;
use OxidEsales\Eshop\Core\Registry;

class Setup extends Base
{
    public static function onActivate(){
        self::addFields();
        self::_rebuildViews();
    }

    public static function addFields(){
        self::addTableIfNotExists();
        self::addColumnIfNotExists('fc_addresses', 'plz', 'ALTER TABLE `fc_addresses` ADD COLUMN `plz` VARCHAR(32);');
        self::addColumnIfNotExists('fc_addresses', 'city', 'ALTER TABLE `fc_addresses` ADD COLUMN `city` VARCHAR(64);');
        self::addColumnIfNotExists('fc_addresses', 'country', 'ALTER TABLE `fc_addresses` ADD COLUMN `country` VARCHAR(644);');
        self::addColumnIfNotExists('fc_addresses', 'country_shortcut', 'ALTER TABLE `fc_addresses` ADD COLUMN `country_shortcut` VARCHAR(8);');
    }

    private static function _rebuildViews(){
        if (Registry::getSession()->getVariable('malladmin')) {
            $metaData = oxnew(DbMetaDataHandler::class);
            $metaData->updateViews();
        }
    }

    private static function addTableIfNotExists(){
        $conn = DatabaseProvider::getDb();

        if ($conn->getOne("SHOW TABLES LIKE 'fc_addresses'") === false) {
            $conn->execute("CREATE TABLE fc_addresses (id VARCHAR(32) PRIMARY KEY);");
        }
    }

    private static function addColumnIfNotExists($sTableName, $sColumnName, $sQuery, $aNewColumnDataQueries = array()): bool
    {
        $aColumns = DatabaseProvider::getDb()->getAll("SHOW COLUMNS FROM {$sTableName} LIKE ?", array($sColumnName));
        if (empty($aColumns)) {
            try {
                DatabaseProvider::getDb()->execute($sQuery);
                foreach ($aNewColumnDataQueries as $sQuery) {
                    DatabaseProvider::getDb()->execute($sQuery);
                }
                return true;
            } catch (\Exception $e) {
                // does nothing yet
            }
        }
        return false;
    }
}