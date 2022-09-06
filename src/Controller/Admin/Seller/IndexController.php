<?php

namespace JOI\Controller\Admin\Seller;

use JOI\Service\CityManager;
use JOI\Service\SellerManager;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends FrameworkBundleAdminController
{

    public function refreshAction() : Response
    {
        // $em = $this->getDoctrine()->getManager();
        $this->addFlash('success', 'X vendeurs mis à jour');
        return $this->redirectToRoute('joi_admin');
    }

    public function detailAction() : Response
    {
        $cityManager = new CityManager();

        return $this->render('@Modules/jumponit/template/admin/seller/index.html.twig', [
            'notLocatedSellers' => SellerManager::getNotLocatedSellers(),
            'cities' => $cityManager->getCities(),
            'action' => 'seller',
        ]);
    }
}