<?php

namespace JOI\Controller\Front\City;

use JOI\Service\CityManager;

class IndexController extends ModuleFrontController
{
    public function __construct() {
        
        $this->cityManager = new CityManager();
    }

    public function listAction($step, $area) {

        return new JsonResponse($this->cityManager->getCitiesByArea($step, $area));
    }
}
