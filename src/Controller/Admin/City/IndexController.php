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
        if ($imported = 1)
        {
            $this->addFlash('success', $imported .' ville importée.');

        } elseif ($imported > 1) {

            $this->addFlash('success', $imported .' villes importées.');

        } else {

            $this->addFlash('warning', 'Aucune ville importée');
        }

        return $this->redirectToRoute('joi_admin_city_detail');
    }

    public function detailAction() : Response
    {

        return $this->render('@Modules/jumponit/template/admin/city/index.html.twig', [
            'action' => 'city',
            'cities' => $this->cityManager->getCities(),
        ]);
    }

    public function toggleActivityAction($id_city, $state) : Response
    {
        $cityName = $this->cityManager->getCityName($id_city);
        $state = $this->cityManager->toggleActivity($id_city, $state, $cityName);

        if ($state == 1)
        {
            $this->addFlash('success', 'Ville '. $cityName .' activée.');

        } else {

            $this->addFlash('success', 'Ville '. $cityName .' désactivée.');
        }

        return $this->redirectToRoute('joi_admin_city_detail');
    }
}
