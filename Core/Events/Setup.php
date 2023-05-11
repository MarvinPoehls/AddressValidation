<?php

namespace MarvinPoehls\AdressValidation\Core\Events;

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

    }

    private static function _rebuildViews(){
        if (Registry::getSession()->getVariable('malladmin')) {
            $metaData = oxnew(DbMetaDataHandler::class);
            $metaData->updateViews();
        }
    }

    public static function addColumnIfNotExists($sTableName, $sColumnName, $sQuery, $aNewColumnDataQueries = array()): bool
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
}