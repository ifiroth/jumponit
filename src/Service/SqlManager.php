<?php

namespace JOI\Service;

use Db;

class SqlManager {

    public function install() {

        $query = "
        DROP TABLE IF EXISTS `". _DB_PREFIX_ ."enabled_city`;
        CREATE TABLE IF NOT EXISTS `". _DB_PREFIX_ ."enabled_city` (
            `id` int NOT NULL AUTO_INCREMENT,
            `feature_value_id` int NOT NULL,
            `isEnabled` bool NOT NULL,
            PRIMARY KEY `id` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        // TODO : Create log action table

        /*
        $query .= "
        DROP TABLE IF EXISTS `". _DB_PREFIX_ ."joi_log_action`;
        CREATE TABLE IF NOT EXISTS `". _DB_PREFIX_ ."joi_log_action` (
            `id` int NOT NULL AUTO_INCREMENT,
            `action` int NOT NULL,
            `timestamp` TIME NOT NULL,
            PRIMARY KEY `id` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";
        */

        return Db::getInstance()->execute($query);
    }

    public function uninstall() {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS `'. _DB_PREFIX_ .'enabled_city`;'
        );
    }

    public function enableCity()
    {


        return true;
    }

    public function disableCity()
    {


        return true;
    }
}
