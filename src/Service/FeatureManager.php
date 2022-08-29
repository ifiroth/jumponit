<?php

namespace JOI\Service;

use JOI\Service\Debug;

class FeatureManager {
    static private $id;
    static private $lang_id;


    public function __construct() {

        $this->context = \Context::getContext();
        self::$id = \Configuration::get(_MOD_PREFIX_.'feature_id');
        self::$lang_id = (int) \Configuration::get('PS_LANG_DEFAULT');
    }

    public function initFeature() : bool
    {
        $feature = new \Feature();
        $feature->name = [ self::$lang_id => "Ville" ];
        $feature->position = \Feature::getHigherPosition() + 1;
        $result = $feature->add();

        \Configuration::updateValue(_MOD_PREFIX_.'feature_id', $feature->id);

        return $result;
    }
    public function deleteFeature() : bool {

        if (self::$id) {
            $feature = new \Feature(self::$id);
            return $feature->delete();

        } else {

            return false;
        }
    }

    static public function hasValueId(string $name) : ?int {
        $values = \FeatureValue::getFeatureValues(self::$id);
        foreach ($values as $value) {
            $featureValue = new \FeatureValue($value['id_feature_value'], self::$lang_id);
            if ($name == $featureValue->value) {
                return $value['id_feature_value'];
            }
        }
        return false;
    }

    static public function createValue(string $name) : ?int {
        $featurevalue = new \FeatureValue;
        $featurevalue->id_feature = self::$id;;
        $featurevalue->value = [ self::$lang_id => $name ];
        $featurevalue->add();

        return $featurevalue->id;
    }
}