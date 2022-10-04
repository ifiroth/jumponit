<?php

ini_set('display_errors', true);

use JOI\Service\CityManager;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

class jumponitcityModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

    }

    public function displayAjaxDefineCity() {

        $lat = (float) Tools::getValue('lat') ?? false;
        $long = (float) Tools::getValue('long') ?? false;
        $postalCode = (int) Tools::getValue('postalCode') ?? false;

        if ($lat && $long) {

            $cityManager = new CityManager();
            $city = $cityManager->locateCityByGPS([$long, $lat]);

            die(\Tools::jsonEncode($city));

        } else if ($postalCode) {

            $cityManager = new CityManager();
            $city = $cityManager->locateCityByPostalCode($postalCode);

            die(\Tools::jsonEncode($city));

        } else {

            die(\Tools::jsonEncode([null]));
        }
    }

    public function displayAjaxSaveCity() {

        $postalCode = (int) Tools::getValue('postalCode') ?? false;

        if ($postalCode != 0) {

            $cityManager = new CityManager();
            $city = $cityManager->locateCityByPostalCode($postalCode);

            if ($city) return \Tools::jsonEncode([$cityManager->saveCity($city, $this->context->customer->id)]);

            return \Tools::jsonEncode([null]);

        } else {

            die(\Tools::jsonEncode([null]));
        }
    }
}
