<?php

namespace Repository;

use Doctrine\ORM\EntityRepository;

class AssetInfoRepository extends EntityRepository {

    public function getSingleReord() {
        global $connection;

        $assets_info = $connection->prepare("SELECT * FROM assets_info WHERE deleted = 0  ORDER BY last_served ASC LIMIT 1 ");
        $assets_info->execute();
        return $assets_info->fetchAll();
    }

    public function updateReord($record_id) {
        global $connection;
        $update = $connection->prepare("UPDATE  assets_info SET deleted = 1  WHERE id = " . $record_id . " ");
        $update->execute();
    }

    public function getDeletedReord($where, $limit) {
        global $connection;
        $assets_info = $connection->prepare("SELECT * FROM assets_info WHERE deleted = 1 $where  ORDER BY last_served ASC $limit ");
        $assets_info->execute();
        return $assets_info->fetchAll();
    }

}
