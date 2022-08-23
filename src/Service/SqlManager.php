<?php

namespace JOI\Service;

class SqlManager {
    public function updateProduct()
    {
        $sql = 'ALTER TABLE `'. _DB_PREFIX_ .'product` ADD `postal` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL';

        \Db::getInstance()->execute($sql);
        return true;
    }
    public function rebuildProduct()
    {
        $sql = 'ALTER TABLE `'. _DB_PREFIX_ .'product` DROP `postal`';
        \Db::getInstance()->execute($sql);
        return true;
    }
}