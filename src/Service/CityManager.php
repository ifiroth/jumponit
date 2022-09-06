<?php

namespace JOI\Service;

use JOI\Service\Debug;

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

        $sql->select('fv.`id_feature_value`, fvl.`value` as name');
        $sql->from('feature_value', 'fv');
        $sql->innerJoin('feature_value_lang', 'fvl', 'fv.`id_feature_value` = fvl.`id_feature_value`');
        $sql->where('fvl.`id_lang` = '. $this->id_lang);

        $cities = \Db::getInstance()->executeS($sql);

        return $cities ?: null;
    }
}