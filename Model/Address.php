<?php

namespace MarvinPoehls\AddressValidation\Model;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Model\MultiLanguageModel;

class Address extends MultiLanguageModel
{
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
        $sql = 'INSERT INTO fc_addresses (`id`, `plz`, `city`, `country`, `country_shortcut`) VALUES ('.$row['id'].', '.$row['plz'].', '.$row['city'].', '.$row['country'].', '.$row['country_shortcut'].')';
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