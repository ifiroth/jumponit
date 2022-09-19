<?php

namespace JOI\Service;

use Db;

class SqlManager {

    private array $sql = [];

    public function __construct() {

        $this->sql['city'] = "
            DROP TABLE IF EXISTS `". _DB_PREFIX_ ."joi_city`;
            CREATE TABLE IF NOT EXISTS `". _DB_PREFIX_ ."joi_city` (
                `id_city` INT NOT NULL AUTO_INCREMENT,
                `id_feature_value` INT,
                `postal_code` INT NOT NULL,
                `nom_comm` VARCHAR(255) NOT NULL,
                `nom_dept` VARCHAR(255) NOT NULL,
                `nom_reg` VARCHAR(255) NOT NULL,
                `statut` VARCHAR(255) NOT NULL,
                `code_reg` INT NOT NULL,
                `code_dept` INT NOT NULL,
                `geo_center` TEXT NOT NULL,
                `geo_shape` TEXT NOT NULL,
                PRIMARY KEY (`id_city`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ";

        $this->sql['product_city'] = "
            DROP TABLE IF EXISTS `". _DB_PREFIX_ ."joi_product_city`;
            CREATE TABLE IF NOT EXISTS `". _DB_PREFIX_ ."joi_product_city` (
                `id_city` INT,
                `id_product` INT,
                PRIMARY KEY (`id_city`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        $this->sql['log_action'] = "
            DROP TABLE IF EXISTS `". _DB_PREFIX_ ."joi_log_action`;
            CREATE TABLE IF NOT EXISTS `". _DB_PREFIX_ ."joi_log_action` (
                `id_log_action` INT NOT NULL AUTO_INCREMENT,
                `action` VARCHAR(50) NOT NULL,
                `value_before` VARCHAR(255) NOT NULL,
                `value_after` VARCHAR(255) NOT NULL,
                `selection` VARCHAR(255) NOT NULL,
                `date_action` TIME NOT NULL,
                PRIMARY KEY (`id_log_action`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ";
    }

    public function install(): bool
    {

        $i = 0;

        foreach ($this->sql as $query)
        {
            $result = Db::getInstance()->execute($query);

            if ($result)
            {
                $i++;
            }
        }

        return (bool) $i;
    }

    public function uninstall(): bool
    {

        $query = '';

        foreach ($this->sql as $key => $value) {

            $query .= 'DROP TABLE IF EXISTS `'. _DB_PREFIX_ . $key .'`;';
        }

        return Db::getInstance()->execute($query);
    }

    public function reset($table) : bool {
        if (array_key_exists($table, $this->sql)) {

            return Db::getInstance()->execute($this->sql[$table]);
        }

        return false;
    }
}
