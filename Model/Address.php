<?php

namespace MarvinPoehls\AddressValidation\Model;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Model\MultiLanguageModel;

class Address extends MultiLanguageModel
{
    protected $_sClassName = 'fc_addresses';

    protected $insert = "";

    public function saveInsert($insert)
    {
        if ($this->insert == "") {
            $this->insert = "INSERT INTO fc_addresses VALUES";
        }
        $this->insert .= "(".$insert."),";
    }

    public function sendInsert()
    {
        if ($this->insert != "") {
            $this->insert = rtrim($this->insert, ",");
            DatabaseProvider::getDb()->execute($this->insert.";");
            $this->insert = "";
        }
    }

    public function getIds(): array
    {
        $conn = DatabaseProvider::getDb();
        $sql = "SELECT oxid FROM fc_addresses";
        $data = $conn->getAll($sql);

        $ids = [];
        foreach ($data as $row) {
            $ids[] = $row[0];
        }

        return $ids;
    }

    public function deleteRows($ids = [])
    {
        if (!empty($ids)) {
            $delete = "delete from fc_addresses where ";
            foreach ($ids as $id) {
                $delete .= "oxid = '".$id."' OR ";
            }
            DatabaseProvider::getDb()->execute(rtrim($delete, "OR "));
        }
    }
}