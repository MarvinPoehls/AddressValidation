<?php

namespace MarvinPoehls\AddressValidation\Model;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Model\MultiLanguageModel;

class Address extends MultiLanguageModel
{
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
        DatabaseProvider::getDb()->execute($this->insert);
        $this->insert = "";
    }

    public function getIds(): array
    {
        $conn = DatabaseProvider::getDb();
        $sql = "SELECT id FROM fc_addresses";
        $data = $conn->getAll($sql);

        $ids = [];
        foreach ($data as $row) {
            $ids[] = $row[0];
        }

        return $ids;
    }

    public function insertCsvRow($row)
    {
        $sql = "INSERT INTO fc_addresses VALUES ('".$row['id']."', '".$row['plz']."', '".$row['city']."', '".$row['country']."', '".$row['country_shortcut']."')";
        DatabaseProvider::getDb()->execute($sql);
    }

    public function deleteRows($ids = [])
    {
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $this->delete($id);
            }
        }
    }
}