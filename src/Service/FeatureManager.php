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
        $result = $feature->add();

        \Configuration::updateValue($this->mod_prefix .'feature_id', $feature->id);

        return $result;
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

    public function resetFeatureValue() : ?int
    {
        if ($this->id) {

            $this->deleteFeature();
            $this->initFeature();

            $cityManger = new CityManager();
            $cities = $cityManger->getCities([], [], 0, 0);

            dump($cities);
            $i = 0;

            foreach ($cities as $city) {

                $this->createValue($city['nom_comm'], $city['id_city']);
                $i++;
            }

            return $i;

        } else {

            return null;
        }
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

    public function createValue(string $name, int $id_city) : ?int
    {
        $featurevalue = new \FeatureValue;
        $featurevalue->id_feature = $this->id;;
        $featurevalue->value = [ $this->lang_id => $name ];
        $featurevalue->add();

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
