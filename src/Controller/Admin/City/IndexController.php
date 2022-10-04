<?php

namespace JOI\Controller\Admin\City;

use JOI\Service\CityManager;
use JOI\Service\FeatureManager;
use JOI\Service\Utils;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends FrameworkBundleAdminController
{
    public function __construct()
    {
        $this->cityManager = new CityManager();
    }

    public function resetFeatureAction($step) : Response
    {
        $featureManager = new FeatureManager();

        if ($step == 0) {
            $featureManager->deleteFeature();
            $featureManager->initFeature();
        }

        $i = $featureManager->resetFeatureValue($step);

        if ($i) {

            return $this->redirectToRoute('joi_admin_city_reset_feature', ['step' => $i]);

        } else {

            if ($step) $this->addFlash('success', $step .' villes caractérisées.');
            else $this->addFlash('warning', 'Aucune ville caractérisée.');
            return $this->redirectToRoute('joi_admin_city_detail');
        }
    }

    public function importAction() : Response
    {
        $imported = $this->cityManager->importCities();

        if ($imported == 1)
        {
            $this->addFlash('success', $imported .' ville importée.');

        } elseif ($imported > 1) {

            $this->addFlash('success', $imported .' villes importées.');

        } else {

            $this->addFlash('warning', 'Aucune ville importée');
        }
        // DONE : remove commentary and restore redirection to feature reset
        return $this->redirectToRoute('joi_admin_city_reset_feature', ['step' => 0]);
        // return $this->redirectToRoute('joi_admin_city_detail');
    }

    public function detailAction(Request $request) : Response
    {
        $sort = $request->query->get('sort');
        $by = ($request->query->get('by') != 'DESC') ? 'ASC' : 'DESC';
        $p = (int) $request->query->get('p') ?: 1;

        $whereClauses = [];

        foreach ($request->request->all() as $key => $value) {
            if ($value) {
                $whereClauses[$key] = $value;
            }
        }

        $citiesCount = $this->cityManager->getCitiesCount();
        $totalPages = (int) round($citiesCount / 50);

        $range = 3;

        $pages = Utils::pagination($p, $totalPages);

        $orderClause = [$sort => $by];

        return $this->render('@Modules/jumponit/template/admin/city/index.html.twig', [
            'action' => 'city',
            'cities' => $this->cityManager->getCities($orderClause, $whereClauses, $p),
            'pages' => $pages,
            'currentPage' => $p,
        ]);
    }

    /* AVORTED
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
    */
}
