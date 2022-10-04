<?php

namespace JOI\Service;

class FeatureManager {

    public function __construct () {

        $this->mod_prefix = \Configuration::get('module_prefix');
        $this->id = \Configuration::get($this->mod_prefix. 'feature_id');
        $this->lang_id = (int) \Configuration::get('PS_LANG_DEFAULT');
    }

    public function initFeature() : bool
    {

        $feature = new \Feature();
        $feature->name = [ $this->lang_id => \Configuration::get($this->mod_prefix .'feature_label') ];
        $feature->position = \Feature::getHigherPosition() + 1;
        $feature->add();

        \Configuration::updateValue($this->mod_prefix .'feature_id', $feature->id);
        $this->id = $feature->id;

        return $feature->id;
    }

    public function deleteFeature() : bool
    {
        if ($this->id) {
            $feature = new \Feature($this->id);
            return $feature->delete();

        } else {

            return false;
        }
    }

    public function resetFeatureValue(int $start = 0) : ?int
    {
        $cityManger = new CityManager();
        $cities = $cityManger->getCities([], [], 0, 0);

        for ($i = $start; $i < count($cities); $i++) {

            if (!$this->createValue($cities[$i]['nom_comm'], $cities[$i]['id_city'])) throw new \PrestaShopException('Impossible de lier la ville '. $cities[$i]['nom_comm']);

            if ($i == (count($cities) - 1)) return null;

            // On évite le 504 timeOut en décomposant la création de feature value en plusieurs étapes
            if ($i >= (10000 + $start)) return $i;
        }

        return $i;
    }

    public function getFeatureValueIdByName(string $name) : ?int
    {
        $values = \FeatureValue::getFeatureValues($this->id);
        foreach ($values as $value) {
            $featureValue = new \FeatureValue($value['id_feature_value'], $this->lang_id);
            if ($name == $featureValue->value) {
                return $value['id_feature_value'];
            }
        }
        return false;
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function createValue(string $name, int $id_city) : ?int
    {
        $featurevalue = new \FeatureValue;
        $featurevalue->id_feature = $this->id;;
        $featurevalue->value = [ $this->lang_id => html_entity_decode($name, ENT_QUOTES) ];

        try {
            if (!$featurevalue->add()) return null;

        } catch (\PrestaShopDatabaseException|\PrestaShopException $e) {
            return null;
        }

        $cityManager = new CityManager();
        $cityManager->linkFeatureToCity($id_city, $featurevalue->id);

        return $featurevalue->id;
    }

    public function countValue($id = null) : ?array
    {
        if (!$id)
            $id = $this->id;

        return \FeatureValue::getFeatureValues($id);
    }

    public function getFeature($id = null) : array
    {
        if (!$id)
            $id = $this->id;

        return \Feature::getFeature($this->lang_id, $id) ?: [ $this->id ];
    }
}
