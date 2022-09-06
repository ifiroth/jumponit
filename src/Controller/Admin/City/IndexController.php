<?php

namespace JOI\Controller\Admin\City;

use JOI\Service\CityManager;
use JOI\Service\ProductManager;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends FrameworkBundleAdminController
{
    public function __construct()
    {
        $this->cityManager = new CityManager();
    }


    public function importAction() : Response
    {
        $imported = $this->cityManager->importCities();

        $this->addFlash('info', $imported);
        return $this->redirectToRoute($redirectToRoute);
    }

    public function detailAction() : Response
    {

        return $this->render('@Modules/jumponit/template/admin/city/index.html.twig', [
            'action' => 'city',
            'cities' => $this->cityManager->getCities(),
        ]);
    }
}
