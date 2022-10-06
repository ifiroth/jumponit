<?php

namespace JOI\Service;

use Db;

class SqlManager {

    // TODO : Uncomment Database drop and creation

    private array $sqlCreate = [];
    private array $sqlAlter = [];

    public function __construct() {

        $this->sqlCreate['city'] = "
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
                `geo_lat` DECIMAL(24, 22) NOT NULL,
                `geo_long` DECIMAL(24, 22) NOT NULL,
                `geo_shape` TEXT NOT NULL,
                PRIMARY KEY (`id_city`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ";

        $this->sqlCreate['product_city'] = "
            DROP TABLE IF EXISTS `". _DB_PREFIX_ ."joi_product_city`;
            CREATE TABLE IF NOT EXISTS `". _DB_PREFIX_ ."joi_product_city` (
                `id_city` INT,
                `id_product` INT,
                PRIMARY KEY (`id_city`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        $this->sqlCreate['log_action'] = "
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

        $this->sqlAlter['customer'] = [
            'add' => ['geo_code', 'INT'],
        ];
    }

    public function install(): bool
    {

        $i = 0;

        /*
        foreach ($this->sqlCreate as $query)
        {
            $result = Db::getInstance()->execute($query);

            if ($result)
            {
                $i++;
            }
        }
        */

        foreach ($this->sqlAlter as $table => $instructions)
        {
            foreach ($instructions as $key => $value) {

                $query = '';

                switch($key) {
                    case 'add':
                        $query = 'ALTER TABLE `'. _DB_PREFIX_ . $table .'` ADD `'. $value[0] .'` '. $value[1];
                        break;
                }

                $result = Db::getInstance()->execute($query);

                if ($result) {
                    $i++;
                }
            }
        }


        return (bool) $i;
    }

    public function uninstall(): bool
    {
        /*
        $query = '';

        foreach ($this->sqlCreate as $key => $value) {

            $query .= 'DROP TABLE IF EXISTS `'. _DB_PREFIX_ . $key .'`;';
        }

        $created = Db::getInstance()->execute($query);
        */
        $query = '';

        foreach ($this->sqlAlter as $table => $instructions) {

            foreach ($instructions as $key => $value) {

                switch($key) {
                    case 'add':
                        $query .= 'ALTER TABLE `'. _DB_PREFIX_ . $table .'` DROP COLUMN '. $value[0] .';';
                        break;
                }
            }
        }

        return Db::getInstance()->execute($query);

        /*
        $altered = Db::getInstance()->execute($query);

        return ($altered && $created);
        */
    }

    public function reset($table) : bool {
        if (array_key_exists($table, $this->sql)) {

            return Db::getInstance()->execute($this->sql[$table]);
        }

        return false;
    }
}
