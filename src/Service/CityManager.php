<?php

namespace JOI\Service;

class CityManager {

    public function __construct() {
        $this->mod_prefix = \Configuration::get('module_prefix');
        $this->feature_id = \Configuration::get($this->mod_prefix .'feature_id');
        $this->id_lang = \Configuration::get('PS_LANG_DEFAULT');
    }

    public function getDisabledCities() : ?array {
        return null;
    }

    public function getCities() : ?array {

        $sql = new \DbQuery();

        $sql->select('c.`id_feature_value`, c.`postal_code`, c.`nom_comm`');
        $sql->from('joi_city', 'c');

        $cities = \Db::getInstance()->executeS($sql);

        return $cities ?: null;
    }

    public function importCities() : bool {

        $mod_prefix = \Configuration::get('module_prefix');
        \Configuration::updateValue($mod_prefix .'last_city_import', time());

        $cityFile = file_get_contents(__DIR__ .'/../../utils/inseeCities.json');
        $cities = json_decode($cityFile);

        foreach ($cities as $city) {
            $db = \Db::getInstance();
            $result = $db->insert('joi_city', [
                'postal_code' => (int) $city['postal_code'],
                'nom_comm' => $city['nom_comm'],
            ]);
        }

        return 'importation';
    }
}
