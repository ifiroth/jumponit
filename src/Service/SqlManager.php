<?php

namespace JOI\Service;

use Db;

class SqlManager {

    public function install() {

        $i = 0;

        $sql[] = "
        DROP TABLE IF EXISTS `". _DB_PREFIX_ ."joi_city`;
        CREATE TABLE IF NOT EXISTS `". _DB_PREFIX_ ."joi_city` (
            `id_city` int NOT NULL AUTO_INCREMENT,
            `id_feature_value` int,
            `postal_code` int NOY NULL,
            `nom_comm` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id_city`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        // TODO : insert other fields from /utils/inseeCities.json

        $sql[] = "
        DROP TABLE IF EXISTS `". _DB_PREFIX_ ."joi_log_action`;
        CREATE TABLE IF NOT EXISTS `". _DB_PREFIX_ ."joi_log_action` (
            `id_log_action` int NOT NULL AUTO_INCREMENT,
            `action` VARCHAR(50) NOT NULL,
            `value_before` VARCHAR(255) NOT NULL,
            `value_after` VARCHAR(255) NOT NULL,
            `selection` VARCHAR(255) NOT NULL,
            `date_action` TIME NOT NULL,
            PRIMARY KEY (`id_log_action`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        foreach ($sql as $query)
        {
            $result = Db::getInstance()->execute($query);

            if ($result)
            {
                $i++;
            } else {
                dump($query);
            }
        }

        return $i;
    }

    public function uninstall() {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS `'. _DB_PREFIX_ .'joi_city`;'.
            'DROP TABLE IF EXISTS `'. _DB_PREFIX_ .'joi_log_action`;'
        );
    }
}
