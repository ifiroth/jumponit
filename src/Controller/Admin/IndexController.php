<?php

namespace JOI\Controller\Admin;

use JOI\Service\SellerManager;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;
use JOI\Service\ProductManager;

class IndexController extends FrameworkBundleAdminController
{

    public function indexAction() : Response
    {
        // $em = $this->getDoctrine()->getManager();

        return $this->render('@Modules/jumponit/template/admin/index.html.twig', [
            'nonLocatedProducts' => ProductManager::getNotLocatedProducts(),
            'nonLocatedSellers' => SellerManager::getNotLocatedSellers(),
        ]);
    }
}