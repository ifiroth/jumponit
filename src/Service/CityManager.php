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

        $sql->select('
            c.`id_feature_value`,
            c.`postal_code`,
            c.`nom_comm`,
            c.`id_city`,
            fp.`id_product` AS product_count,
            sp.`id_seller` AS seller_count
        ');
        $sql->from('joi_city', 'c');
        $sql->leftJoin('feature_product', 'fp', 'fp.`id_feature_value` = c.`id_feature_value`');
        $sql->leftJoin('seller_product', 'sp', 'sp.`id_product` = fp.`id_product`');
        $sql->orderBy('c.`id_feature_value` DESC, c.`nom_comm` ASC');
        $sql->groupBy('c.`nom_comm`');
        // TODO : pagination instead of limit 50
        $sql->limit(50);

        // TODO : Optimize query with COUNT(fp.`id_product`) AS product_count, + id_seller

        $cities = \Db::getInstance()->executeS($sql);

        return $cities ?: null;
    }

    public function importCities() : int {

        $mod_prefix = \Configuration::get('module_prefix');
        \Configuration::updateValue($mod_prefix .'last_city_import', time());

        $i = 0;

        $cityFile = file_get_contents(__DIR__ .'/../../utils/inseeCities.json');
        $cities = json_decode($cityFile, true);

        $sqlManager = new SqlManager();
        $sqlManager->reset('city');

        foreach ($cities as $city) {
            $db = \Db::getInstance();
            $fields = $city['fields'];

            $db->insert('joi_city', [
                'postal_code' => (int) $fields['postal_code'],
                'code_dept' => (int) $fields['code_dept'],
                'code_reg' => (int) $fields['code_reg'],
                'nom_comm' => htmlentities($fields['nom_comm'], ENT_QUOTES),
                'nom_dept' => htmlentities($fields['nom_dept'], ENT_QUOTES),
                'nom_reg' => htmlentities($fields['nom_reg'], ENT_QUOTES),
                'latitude' => (int) $fields['geo_point_2d'][0],
                'longitude' => (int) $fields['geo_point_2d'][1],
                'geo_shape' => json_encode($fields['geo_shape']['coordinates']),
                'statut' => $fields['statut']
            ]);

            $i++;
        }

        return $i;
    }

    public function getCityName($id_city) : ?string {
        $sql = new \DbQuery();

        $sql->select('c.`nom_comm`');
        $sql->from('joi_city', 'c');
        $sql->where('c.`id_city` = '. $id_city);

        $result = \Db::getInstance()->getRow($sql);

        return $result['nom_comm'];
    }

    public function getCityByArea($step, $areaCode) {

        $sql = new \DbQuery();

        $sql->select('c.`nom_comm`');
        $sql->from('joi_city', 'c');

        switch ($step) {
            case 'dept':
                $sql->where('c.`code_dept` = '. $areaCode);
                break;

            case 'reg':
                $sql->where('c.`code_reg` = '. $areaCode);
                break;

            default:
                $sql->where();
                break;
        }

        $result = \Db::getInstance()->getRow($sql);

        return $result['nom_comm'];
    }

    public function getCityByName($name) : ?int {
        $sql = new \DbQuery();

        $sql->select('c.`id_city`');
        $sql->from('joi_city', 'c');
        $sql->where('c.`nom_comm` = "'. htmlentities(strtoupper($name)) .'"');

        dump($name);

        $result = \Db::getInstance()->getRow($sql);

        return $result ? $result['id_city'] : null;
    }

    public function toggleActivity($id_city, $state, $name) : ?bool {

        if ($state) {

            $id_feature_value = FeatureManager::hasValueId($id_city);

            if (!$id_feature_value) {

                $id_feature_value = FeatureManager::createValue($name);
            }

        } else {
            $id_feature_value = null;
        }

        $db = \Db::getInstance();

        $result = $db->update('joi_city', [
            'id_feature_value' => $id_feature_value
        ], 'id_city = '. $id_city, 1);

        return $result;

    }

    public function linkFeatureToCity($id_city, $id_feature_value) {

        $db = \Db::getInstance();

        dump($id_city);

        $result = $db->update('joi_city', [
            'id_feature_value' => $id_feature_value
        ], 'id_city = '. $id_city, 1);

        dump($result);

        return $result;
    }
}
