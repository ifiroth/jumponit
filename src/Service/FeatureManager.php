<?php

namespace JOI\Service;

class FeatureManager {
    static private $id;
    static private $lang_id;


    public function __construct() {

        $this->context = \Context::getContext();
        self::$id = \Configuration::get(_MOD_PREFIX_.'feature_id');
        self::$lang_id = \Configuration::get('PS_LANG_DEFAULT');
    }

    public function initFeature() : bool
    {
        $feature = new \Feature();
        $feature->name = "Ville";
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

    static public function hasValue(string $name) : bool {
        // TODO : check if a value already exist

        return false;
    }

    static public function createValue(string $name) : ?int {
        $featurevalue = new \FeatureValue;
        $featurevalue->id_feature = self::$id;;
        $featurevalue->value = $name;
        $featurevalue->add();

        return $featurevalue->id;
    }

    static public function getValueId(string $name) : ?int {
        // TODO : getValueId()

        return 1;
    }
}