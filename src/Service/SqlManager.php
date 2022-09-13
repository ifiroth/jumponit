<?php

namespace JOI\Service;

use Db;

class SqlManager {

    public function __construct() {
        /*
        $this->sql['city'] = "
            DROP TABLE IF EXISTS `". _DB_PREFIX_ ."joi_city`;
            CREATE TABLE IF NOT EXISTS `". _DB_PREFIX_ ."joi_city` (
                `id_city` int NOT NULL AUTO_INCREMENT,
                `id_feature_value` int,
                `postal_code` int NOT NULL,
                `nom_comm` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`id_city`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ";
        */
        // TODO : Uncomment $this->sql['city'] declaration

        $this->sql['log_action'] = "
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
    }

    public function install() {

        $i = 0;

        foreach ($this->sql as $query)
        {
            $result = Db::getInstance()->execute($query);

            if ($result)
            {

                $i++;
            }
        }

        return $i;
    }

    public function uninstall() {

        $query = '';

        foreach ($this->sql as $key => $value) {

            $query .= 'DROP TABLE IF EXISTS `'. _DB_PREFIX_ . $key .'`;';
        }

        return Db::getInstance()->execute($query);
    }

    public function reset($table) : bool {
        if (in_array($table, $this->sql)) {

            return Db::getInstance()->execute($sql[$table]);
        }

        return false;
    }
}
