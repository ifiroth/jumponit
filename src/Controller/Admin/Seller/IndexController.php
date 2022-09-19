<?php

namespace JOI\Controller\Admin\Seller;

use JOI\Service\CityManager;
use JOI\Service\SellerManager;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends FrameworkBundleAdminController
{
    public function __construct()
    {
        $this->mod_prefix = \Configuration::get('module_prefix');
    }


    public function refreshAction() : Response
    {
        // $em = $this->getDoctrine()->getManager();
        $this->addFlash('success', 'X vendeurs mis à jour');
        return $this->redirectToRoute('joi_admin');
    }

    public function detailAction() : Response
    {
        $cityManager = new CityManager();
        $sellerManager = new SellerManager();

        return $this->render('@Modules/jumponit/template/admin/seller/index.html.twig', [
            'notLocatedSellers' => $sellerManager->getNotLocatedSellers(),
            'sellers' => $sellerManager->getSellers(),
            'cities' => $cityManager->getCities(),
            'action' => 'seller',
        ]);
    }

    public function generalWarningAction() : Response
    {
        // TODO : Define generalWarningAction

        $flash = 'warning';
        $message = 'Fonction non programmée.';

        \Configuration::updateValue($this->mod_prefix .'last_seller_general_warning', time());

        $this->addFlash($flash, $message);
        return $this->redirectToRoute('joi_admin');
    }
}
