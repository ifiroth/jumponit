<?php

namespace JOI\Service;

use JOI\Service\Debug;

class FeatureManager {
    static private $id;
    static private $lang_id;
    static private $mod_prefix;

    public static function __initStatic () {

        self::$mod_prefix = \Configuration::get('module_prefix');
        self::$id = \Configuration::get(self::$mod_prefix. 'feature_id');
        self::$lang_id = (int) \Configuration::get('PS_LANG_DEFAULT');
    }

    public function initFeature() : bool
    {
        self::__initStatic();

        $feature = new \Feature();
        $feature->name = [ self::$lang_id => \Configuration::get(self::$mod_prefix .'feature_label') ];
        $feature->position = \Feature::getHigherPosition() + 1;
        $result = $feature->add();

        \Configuration::updateValue(self::$mod_prefix .'feature_id', $feature->id);

        return $result;
    }

    public function deleteFeature() : bool
    {
        self::__initStatic();

        if (self::$id) {
            $feature = new \Feature(self::$id);
            return $feature->delete();

        } else {

            return false;
        }
    }

    static public function hasValueId(string $name) : ?int
    {
        self::__initStatic();

        $values = \FeatureValue::getFeatureValues(self::$id);
        foreach ($values as $value) {
            $featureValue = new \FeatureValue($value['id_feature_value'], self::$lang_id);
            if ($name == $featureValue->value) {
                return $value['id_feature_value'];
            }
        }
        return false;
    }

    static public function createValue(string $name) : ?int
    {
        self::__initStatic();

        $featurevalue = new \FeatureValue;
        $featurevalue->id_feature = self::$id;;
        $featurevalue->value = [ self::$lang_id => $name ];
        $featurevalue->add();

        return $featurevalue->id;
    }

    static public function countValue($id = null) : ?array
    {
        self::__initStatic();

        if (!$id)
            $id = self::$id;

        return \FeatureValue::getFeatureValues($id);
    }

    static public function getFeature($id = null) : array
    {
        self::__initStatic();

        if (!$id)
            $id = self::$id;

        return \Feature::getFeature(self::$lang_id, $id) ?: [ self::$id ];
    }
}